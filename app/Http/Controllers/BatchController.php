<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\GenerateFlashcardsInBulk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BatchController extends Controller
{
    public function index()
    {
        $batches = auth()->user()->batches()->latest()->paginate(10);
        return view('batches.index', compact('batches'));
    }

    public function show(Batch $batch)
    {
        $this->authorize('view', $batch);
        $cards = $batch->cards()->paginate(100);

        return view('batches.show', compact('batch', 'cards'));
    }

    public function destroy(Batch $batch)
    {
        $this->authorize('delete', $batch);
        $batch->delete();
        return redirect()->route('home')->with('success', 'Batch deleted successfully.');
    }

    public function update(Request $request, Batch $batch)
    {
        $this->authorize('update', $batch);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $batch->update([
            'name' => $request->name,
        ]);

        return response()->json(['success' => true]);
    }

    public function retry(Request $request, Batch $batch)
    {
        $this->authorize('update', $batch);

        if ($batch->status !== 'failed') {
            return back()->withErrors(['message' => 'Only failed batches can be retried.']);
        }

        if (empty($batch->input_vocabulary)) {
            return back()->withErrors(['message' => 'Cannot retry this batch because the input vocabulary is missing.']);
        }

        $words = array_filter(array_map('trim', explode("\n", $batch->input_vocabulary)));
        $cost = count($words);
        $user = $request->user();

        try {
            DB::transaction(function () use ($user, $cost, $words, $batch) {
                // Lock user for update
                $user = User::lockForUpdate()->find($user->id);

                $apiKey = null;
                $isSystemKey = false;

                if ($user->free_cards_remaining > 0) {
                    // User is in "Free Trial" mode
                    if ($cost > $user->free_cards_remaining) {
                         throw new \DomainException("You don't have enough free credits to retry. You need {$cost} credits.");
                    }

                    // Deduct credits
                    $user->decrement('free_cards_remaining', $cost);

                    $apiKey = Config::get('services.openai.key');
                    $isSystemKey = true;
                } else {
                    // User is in "Paid" mode
                    if (empty($user->openai_api_key)) {
                         throw new \DomainException("You have no free credits left. Please add your OpenAI API Key in Settings.");
                    }
                    $apiKey = $user->openai_api_key;
                }

                // Validate OpenAI Key
                $this->validateOpenAIKey($apiKey, $isSystemKey);

                // Reset batch status
                $batch->update([
                    'status' => 'pending', 
                    'error_message' => null
                ]);

                // Clear existing cards to avoid duplicates? 
                // Since this is a "bulk" generation that might have partially succeeded, 
                // maybe we should NOT clear? But the job generates ALL cards for the list.
                // If we don't clear, we get duplicates.
                // Let's clear them for a clean retry.
                $batch->cards()->delete();

                $wordChunks = array_chunk($words, 20);

                foreach ($wordChunks as $chunk) {
                    if (!empty($chunk)) {
                        GenerateFlashcardsInBulk::dispatch($chunk, $user, $batch, $batch->prompt_id, $apiKey);
                    }
                }
            });
        } catch (\DomainException $e) {
             return back()->withErrors(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Error while retrying batch.', [
                'batch_id' => $batch->id,
                'exception' => $e,
            ]);
            return back()->withErrors(['message' => 'An unexpected error occurred. Please try again later.']);
        }

        return back()->with('success', 'Batch retry started.');
    }

    private function validateOpenAIKey(string $apiKey, bool $isSystemKey)
    {
        try {
            $response = Http::withToken($apiKey)->get('https://api.openai.com/v1/models');
        } catch (\Exception $e) {
            if ($isSystemKey) {
                Log::error('OpenAI Connection Failed (System Key)', ['exception' => $e]);
                throw new \DomainException('Unable to connect to OpenAI services. Please try again later.');
            }
            throw new \DomainException('Unable to connect to OpenAI to validate your key.');
        }

        if ($response->successful()) {
            return;
        }

        $status = $response->status();
        $data = $response->json();
        $errorCode = $data['error']['code'] ?? null;

        if ($status === 401) {
             if ($isSystemKey) {
                 Log::critical('System OpenAI Key is invalid!');
                 throw new \DomainException('System configuration error. The admin has been notified.');
             }
             throw new \DomainException('Your OpenAI API Key is invalid. Please check it in Settings.');
        }

        if ($status === 429 && $errorCode === 'insufficient_quota') {
             if ($isSystemKey) {
                 Log::critical('System OpenAI Key has insufficient quota!');
                 throw new \DomainException('The system is currently out of AI credits. The admin has been notified.');
             }
             throw new \DomainException('You have insufficient credits on your OpenAI account. Please charge your account or adjust your spending limit at platform.openai.com.');
        }

        if ($isSystemKey) {
            Log::error('OpenAI System Key Check Failed', ['status' => $status, 'body' => $response->body()]);
            throw new \DomainException('An error occurred with the AI service. Please try again later.');
        }
        
        throw new \DomainException('OpenAI API Error: ' . ($data['error']['message'] ?? 'Unknown error'));
    }
}
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

        // Determine and validate key before transaction
        $apiKey = null;
        $isSystemKey = false;

        if ($user->free_cards_remaining > 0) {
            $apiKey = Config::get('services.openai.key');
            $isSystemKey = true;
        } else {
            if (empty($user->openai_api_key)) {
                return back()->withErrors(['message' => "You have no free credits left. Please add your OpenAI API Key in Settings."]);
            }
            $apiKey = $user->openai_api_key;
        }

        try {
            $this->validateOpenAIKey($apiKey, $isSystemKey);
        } catch (\DomainException $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }

        try {
            DB::transaction(function () use ($user, $cost, $words, $batch, $isSystemKey) {
                // Lock user for update
                $user = User::lockForUpdate()->find($user->id);

                // Re-check credits inside transaction to be safe
                // Note: We used $isSystemKey from outside snapshot. 
                // If user lost credits in between, we fall back to logic:
                // If intended to use System Key but ran out -> Fail? Or switch to User Key?
                // For simplicity and safety: If we intended System Key (Free), we MUST have credits.
                
                if ($isSystemKey) {
                    if ($user->free_cards_remaining < $cost) {
                         throw new \DomainException("You don't have enough free credits to retry. You need {$cost} credits.");
                    }
                    $user->decrement('free_cards_remaining', $cost);
                    $apiKey = Config::get('services.openai.key');
                } else {
                    // Intended to use User Key.
                    // Even if they gained credits now, we stick to User Key as validated?
                    // Or we just check if they have a key.
                    if (empty($user->openai_api_key)) {
                         throw new \DomainException("You have no free credits left. Please add your OpenAI API Key in Settings.");
                    }
                    $apiKey = $user->openai_api_key;
                }

                // Reset batch status
                $batch->update([
                    'status' => 'pending', 
                    'error_message' => null
                ]);

                // NOTE: Do NOT clear existing cards here.
                // The batch may have partially succeeded before failing. If we delete
                // existing cards and then re-charge for the full word list, users lose
                // both their previously generated cards and the credits they spent.
                // Instead, we keep existing cards and allow the retry to generate any
                // missing ones, even if that may result in some duplicates.
                // $batch->cards()->delete(); 

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
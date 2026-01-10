<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\GenerateFlashcardsInBulk;
use App\Jobs\GenerateTts;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Services\OpenAIKeyResolver;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'vocabulary' => 'required|string',
            'prompt_id' => 'required|exists:prompts,id',
        ]);

        $words = array_filter(array_map('trim', explode("\n", $request->vocabulary)));
        $cost = count($words);

        if ($cost === 0) {
            return back()->withErrors(['vocabulary' => 'Please enter at least one word.']);
        }

        $user = $request->user();

        try {
            DB::transaction(function () use ($user, $cost, $words, $request) {
                // Lock user for update to prevent race conditions
                $user = User::lockForUpdate()->find($user->id);

                $apiKey = null;
                $isSystemKey = false;

                if ($user->free_cards_remaining > 0) {
                    // User is in "Free Trial" mode (has credits)
                    if ($cost > $user->free_cards_remaining) {
                         throw new \DomainException("You don't have enough free credits to create that many cards. You have {$user->free_cards_remaining} credits left.");
                    }

                    // Deduct credits
                    $user->decrement('free_cards_remaining', $cost);

                    $apiKey = \Illuminate\Support\Facades\Config::get('services.openai.key');
                    $isSystemKey = true;
                } else {
                    // User is in "Paid" mode (no credits)
                    if (empty($user->openai_api_key)) {
                         throw new \DomainException("You have no free credits left. Please add your OpenAI API Key in Settings.");
                    }
                    $apiKey = $user->openai_api_key;
                }

                // Validate OpenAI Key before proceeding
                $this->validateOpenAIKey($apiKey, $isSystemKey);

                $batch = Batch::create([
                    'user_id' => $user->id,
                    'name' => 'Batch',
                    'input_vocabulary' => $request->vocabulary,
                    'prompt_id' => $request->prompt_id,
                ]);
                $batch->update(['name' => 'Batch ' . $batch->id]);

                $wordChunks = array_chunk($words, 20);

                foreach ($wordChunks as $chunk) {
                    if (!empty($chunk)) {
                        GenerateFlashcardsInBulk::dispatch($chunk, $user, $batch, $request->prompt_id, $apiKey);
                    }
                }
            });
        } catch (\DomainException $e) {
             return back()->withErrors(['vocabulary' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            Log::error('Error while storing flashcard generation request.', [
                'user_id' => optional($request->user())->id ?? null,
                'exception' => $e,
            ]);
            return back()
                ->withErrors(['vocabulary' => 'An unexpected error occurred while processing your request. Please try again later.'])
                ->withInput();
        }

        return redirect()->route('home')->with('success', 'Your request has been submitted. The flashcards will be generated in the background.');
    }

    private function validateOpenAIKey(string $apiKey, bool $isSystemKey)
    {
        try {
            $response = Http::withToken($apiKey)->get('https://api.openai.com/v1/models');
        } catch (\Exception $e) {
            // Network error or other connection issue
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

        // Generic fallback for other errors
        if ($isSystemKey) {
            Log::error('OpenAI System Key Check Failed', ['status' => $status, 'body' => $response->body()]);
            throw new \DomainException('An error occurred with the AI service. Please try again later.');
        }
        
        throw new \DomainException('OpenAI API Error: ' . ($data['error']['message'] ?? 'Unknown error'));
    }

    public function buildAnkiNotes(Request $request)
    {
        $request->validate([
            'cards' => 'required|array',
            'deck' => 'required|string',
        ]);

        $cards = Card::find($request->input('cards'));

        $notes = $cards->map(function ($card) use ($request) {
            $note = [
                'deckName' => $request->input('deck'),
                'modelName' => 'Basic',
                'fields' => [
                    'Front' => $card->front,
                    'Back' => nl2br($card->back),
                ],
                'options' => [
                    'allowDuplicate' => true,
                ],
                'tags' => [
                    'anki-plattform',
                ]
            ];

            if ($card->audio_path && Storage::disk('public')->exists($card->audio_path)) {
                $note['audio'] = [[
                    'data' => base64_encode(Storage::disk('public')->get($card->audio_path)),
                    'filename' => basename($card->audio_path),
                    'fields' => ['Back']
                ]];
            }

            return $note;
        })->toArray();

        return response()->json(['notes' => $notes]);
    }

    public function update(Request $request, Card $card)
    {
        $this->authorize('update', $card);

        $request->validate([
            'front' => 'required|string|max:255',
            'back' => 'required|string|max:255',
            'tts' => 'required|string|max:255',
        ]);

        $ttsChanged = $card->tts !== $request->tts;

        $card->update([
            'front' => $request->front,
            'back' => $request->back,
            'tts' => $request->tts,
        ]);

        if ($ttsChanged) {
            if ($card->audio_path && Storage::disk('public')->exists($card->audio_path)) {
                Storage::disk('public')->delete($card->audio_path);
            }

            GenerateTts::dispatchSync($card, OpenAIKeyResolver::resolve($card->user));
            $card->refresh();
        }

        return response()->json([
            'success' => true,
            'audio_path' => $ttsChanged ? Storage::url($card->audio_path) : null,
        ]);
    }
}

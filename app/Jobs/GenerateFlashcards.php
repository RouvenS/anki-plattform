<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Batch;
use App\Models\Prompt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\GenerateTts;

class GenerateFlashcards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $word;
    protected User $user;
    protected Batch $batch;
    protected int $promptId;

    public function __construct(string $word, User $user, Batch $batch, int $promptId)
    {
        $this->word     = $word;
        $this->user     = $user;
        $this->batch    = $batch;
        $this->promptId = $promptId;
    }

    public function handle(): void
    {
        $prompt = Prompt::find($this->promptId);
        if (!$prompt) {
            Log::error('Prompt not found', ['prompt_id' => $this->promptId]);
            return;
        }

        // --- 1) Build schema for Structured Outputs
        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['cards'],
            'properties' => [
                'cards' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => 5, // be generous; you can tune
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['front', 'back', 'tts'],
                        'properties' => [
                            'front' => ['type' => 'string'],
                            'back'  => ['type' => 'string'],
                            'tts'   => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ];

        // --- 2) Compose instructions + input
        $system = "You generate Russian–English flash cards that strictly follow linguistic rules and return JSON matching the provided schema.";
        $user   = $prompt->prompt . "\n\nINPUT LEXEME:\n" . $this->word;

        try {
            $resp = Http::timeout(60)
                ->withToken($this->user->openai_api_key)
                ->post('https://api.openai.com/v1/responses', [
                    // Pick a model that supports Structured Outputs.
                    // If you prefer a smaller/cheaper one, use 'gpt-4o-mini-2024-07-18'.
                    'model' => 'gpt-4o-2024-08-06',

                    'input' => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user',   'content' => $user],
                    ],

                    // Force the JSON schema
                    'text' => [
                        'format' => [
                            'type'   => 'json_schema',
                            'name'   => 'flash_cards',
                            'schema' => $schema,
                            'strict' => true,
                        ],
                    ],

                    // Optional; prevents truncation
                    'max_output_tokens' => 800,
                ]);

            if ($resp->failed()) {
                Log::error('OpenAI Responses API failed', [
                    'status'   => $resp->status(),
                    'response' => $resp->body(),
                ]);
                return;
            }

            $data = $resp->json();

            // Handle refusals / incomplete generations explicitly
            if (($data['status'] ?? null) !== 'completed') {
                Log::error('OpenAI response incomplete', ['response' => $data]);
                return;
            }

            // Prefer the aggregated output_text; fall back to scanning content items.
            $jsonText = $data['output_text'] ?? null;

            if (!$jsonText) {
                // Look for output_text content item
                $items = $data['output'][0]['content'] ?? [];
                foreach ($items as $item) {
                    if (($item['type'] ?? null) === 'refusal') {
                        Log::warning('Model refusal', ['refusal' => $item['refusal'] ?? '']);
                        return;
                    }
                    if (($item['type'] ?? null) === 'output_text') {
                        $jsonText = $item['text'] ?? null;
                        break;
                    }
                }
            }

            if (!$jsonText) {
                Log::error('No output_text found in OpenAI response', ['response' => $data]);
                return;
            }

            // Parse JSON (schema guarantees structure)
            $parsed = json_decode($jsonText, true);
            if (!is_array($parsed) || !isset($parsed['cards']) || !is_array($parsed['cards'])) {
                Log::error('Parsed JSON missing expected "cards" array', ['parsed' => $parsed]);
                return;
            }

            foreach ($parsed['cards'] as $cardData) {
                // Double-check required fields
                if (!isset($cardData['front'], $cardData['back'], $cardData['tts'])) {
                    Log::error('Card missing required keys', ['card' => $cardData]);
                    continue;
                }

                $card = $this->user->cards()->create([
                    'front'    => $cardData['front'],
                    'back'     => $cardData['back'],
                    'tts'      => $cardData['tts'],
                    'batch_id' => $this->batch->id,
                ]);

                GenerateTts::dispatch($card);
            }
        } catch (\Throwable $e) {
            Log::error('Error generating flashcards', ['exception' => $e]);
        }
    }
}
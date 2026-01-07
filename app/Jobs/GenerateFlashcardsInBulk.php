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

class GenerateFlashcardsInBulk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $words;
    protected User $user;
    protected Batch $batch;
    protected int $promptId;
    protected string $apiKey;

    public function __construct(array $words, User $user, Batch $batch, int $promptId, string $apiKey)
    {
        $this->words    = $words;
        $this->user     = $user;
        $this->batch    = $batch;
        $this->promptId = $promptId;
        $this->apiKey   = $apiKey;
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
            'required' => ['word_cards'],
            'properties' => [
                'word_cards' => [
                    'type' => 'array',
                    'description' => 'An array where each element corresponds to a word from the input.',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['word', 'cards'],
                        'properties' => [
                            'word' => ['type' => 'string', 'description' => 'The input word.'],
                            'cards' => [
                                'type' => 'array',
                                'minItems' => 1,
                                'maxItems' => 5,
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
                    ],
                ],
            ],
        ];

        // --- 2) Compose instructions + input
        $system = "You generate Russian–English flash cards for a list of words. You strictly follow linguistic rules and return a single JSON object matching the provided schema. For each word in the input, you provide an object with the word and an array of cards.";
        $user   = $prompt->prompt . "\n\nINPUT LEXEMES:\n" . implode("\n", $this->words);

        try {
            $resp = Http::timeout(120) // Increased timeout for bulk processing
                ->withToken($this->apiKey)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => 'gpt-4o-2024-08-06',
                    'input' => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user',   'content' => $user],
                    ],
                    'text' => [
                        'format' => [
                            'type'   => 'json_schema',
                            'name'   => 'flash_cards_bulk',
                            'schema' => $schema,
                            'strict' => true,
                        ],
                    ],
                    'max_output_tokens' => 4096, // Increased max tokens for bulk processing
                ]);

            if ($resp->failed()) {
                Log::error('OpenAI Responses API failed', [
                    'status'   => $resp->status(),
                    'response' => $resp->body(),
                ]);
                return;
            }

            $data = $resp->json();

            if (($data['status'] ?? null) !== 'completed') {
                Log::error('OpenAI response incomplete', ['response' => $data]);
                return;
            }

            $jsonText = $data['output_text'] ?? null;

            if (!$jsonText) {
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

            $parsed = json_decode($jsonText, true);
            if (!is_array($parsed) || !isset($parsed['word_cards']) || !is_array($parsed['word_cards'])) {
                Log::error('Parsed JSON missing expected "word_cards" array', ['parsed' => $parsed]);
                return;
            }

            foreach ($parsed['word_cards'] as $wordCardData) {
                if (!isset($wordCardData['cards']) || !is_array($wordCardData['cards'])) {
                    continue;
                }
                foreach ($wordCardData['cards'] as $cardData) {
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

                    GenerateTts::dispatch($card, $this->apiKey);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error generating flashcards in bulk', ['exception' => $e]);
        }
    }
}

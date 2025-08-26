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

    protected $word;
    protected $user;
    protected $batch;
    protected $promptId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $word, User $user, Batch $batch, int $promptId)
    {
        $this->word = $word;
        $this->user = $user;
        $this->batch = $batch;
        $this->promptId = $promptId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $prompt = Prompt::find($this->promptId);
        if (!$prompt) {
            Log::error('Prompt not found', ['prompt_id' => $this->promptId]);
            return;
        }

        $promptContent = $prompt->prompt;
        $promptContent .= "\n\n" . $this->word;

        try {
            $response = Http::withToken($this->user->openai_api_key)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [['role' => 'user', 'content' => $promptContent]],
                'temperature' => 0.2,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI API request failed', ['response' => $response->body()]);
                return;
            }

            Log::info('OpenAI response for flashcards', ['response' => $response->body()]);

            $cards = json_decode($response->body(), true)['choices'][0]['message']['content'];
            $cards = json_decode($cards, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode json from OpenAI', ['response' => $response->body()]);
                return;
            }

            Log::info('Decoded cards', ['cards' => $cards]);

            foreach ($cards as $cardData) {
                if (!isset($cardData['front']) || !isset($cardData['back']) || !isset($cardData['tts'])) {
                    Log::error('Invalid card data from OpenAI', ['card' => $cardData]);
                    continue;
                }

                $cardData['batch_id'] = $this->batch->id;
                $card = $this->user->cards()->create($cardData);
                GenerateTts::dispatch($card);
            }
        } catch (\Exception $e) {
            Log::error('Error generating flashcards', ['exception' => $e->getMessage()]);
        }
    }
}

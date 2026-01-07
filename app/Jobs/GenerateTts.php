<?php

namespace App\Jobs;

use App\Models\Card;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Services\OpenAIKeyResolver;

class GenerateTts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $card;
    protected $apiKey;

    /**
     * Create a new job instance.
     */
    public function __construct(Card $card, ?string $apiKey = null)
    {
        $this->card = $card;
        $this->apiKey = $apiKey;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Generating TTS for card: ' . $this->card->id, ['input' => $this->card->tts]);
        
        $key = $this->apiKey ?? OpenAIKeyResolver::resolve($this->card->user);

        if (!$key) {
            Log::error('No OpenAI API Key available for TTS generation');
            return;
        }

        try {
            $response = Http::withToken($key)->post('https://api.openai.com/v1/audio/speech', [
                'model' => 'gpt-4o-mini-tts',
                'input' => $this->card->tts,
                'voice' => 'alloy',
            ]);

            if ($response->failed()) {
                Log::error('OpenAI TTS API request failed', ['response' => $response->body()]);
                return;
            }

            $path = 'audio/' . $this->card->id . '.mp3';
            Storage::disk('public')->put($path, $response->body());

            $this->card->update([
                'audio_path' => $path,
                'expires_at' => Carbon::now()->addDays(7),
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating TTS', ['exception' => $e->getMessage()]);
        }
    }
}
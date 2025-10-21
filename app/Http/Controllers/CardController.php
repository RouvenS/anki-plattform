<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Jobs\GenerateFlashcardsInBulk;
use App\Jobs\GenerateTts;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'vocabulary' => 'required|string',
            'prompt_id' => 'required|exists:prompts,id',
        ]);

        $words = explode("\n", $request->vocabulary);
        $user = $request->user();

        $batch = $user->batches()->create([
            'name' => 'Batch from ' . now()->format('Y-m-d H:i:s'),
        ]);

        $wordChunks = array_chunk(array_filter(array_map('trim', $words)), 20);

        foreach ($wordChunks as $chunk) {
            if (!empty($chunk)) {
                GenerateFlashcardsInBulk::dispatch($chunk, $user, $batch, $request->prompt_id);
            }
        }

        return redirect()->route('home')->with('success', 'Your request has been submitted. The flashcards will be generated in the background.');
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

            GenerateTts::dispatchSync($card);
            $card->refresh();
        }

        return response()->json([
            'success' => true,
            'audio_path' => $ttsChanged ? Storage::url($card->audio_path) : null,
        ]);
    }
}

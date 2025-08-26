<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Jobs\GenerateFlashcards;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'vocabulary' => 'required|string',
        ]);

        $words = explode("\n", $request->vocabulary);
        $user = $request->user();

        $batch = $user->batches()->create([
            'name' => 'Batch from ' . now()->format('Y-m-d H:i:s'),
        ]);

        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word)) {
                GenerateFlashcards::dispatch($word, $user, $batch);
            }
        }

        return redirect()->route('home')->with('success', 'Your request has been submitted. The flashcards will be generated in the background.');
    }

    public function addToAnki(Request $request)
    {
        $request->validate([
            'cards' => 'required|array',
            'deck' => 'required|string',
            'batch_id' => 'required|exists:batches,id',
        ]);

        $cards = Card::find($request->input('cards'));

        $notes = $cards->map(function ($card) use ($request) {
            $note = [
                'deckName' => $request->input('deck'),
                'modelName' => 'Basic',
                'fields' => [
                    'Front' => $card->front,
                    'Back' => $card->back,
                ],
            ];

            if ($card->audio_path && Storage::disk('public')->exists($card->audio_path)) {
                $note['audio'] = [
                    [
                        'data' => base64_encode(Storage::disk('public')->get($card->audio_path)),
                        'filename' => basename($card->audio_path),
                        'fields' => [
                            'Back'
                        ]
                    ]
                ];
            }

            return $note;
        })->toArray();

        try {
            $response = Http::timeout(60)->post('http://127.0.0.1:8765', [
                'action' => 'addNotes',
                'version' => 6,
                'params' => [
                    'notes' => $notes,
                ],
            ]);

            if ($response->ok() && $response->json()['error'] === null) {
                return redirect()->route('batches.show', $request->input('batch_id'))->with('success', 'Cards added to Anki successfully.');
            } else {
                return redirect()->route('batches.show', $request->input('batch_id'))->with('error', 'Failed to add cards to Anki: ' . $response->json()['error']);
            }
        } catch (ConnectionException $e) {
            return redirect()->route('batches.show', $request->input('batch_id'))->with('error', 'Could not connect to Anki. Is it running?');
        }
    }
}

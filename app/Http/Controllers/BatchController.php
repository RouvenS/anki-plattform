<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

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
        $cards = $batch->cards()->paginate(10);

        $decks = [];
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(2)->post('http://127.0.0.1:8765', [
                'action' => 'deckNames',
                'version' => 6,
            ]);

            if ($response->ok()) {
                $decks = $response->json()['result'];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Anki is not running or AnkiConnect is not available.
            // Silently ignore and return an empty array of decks.
        }

        return view('batches.show', compact('batch', 'cards', 'decks'));
    }

    public function destroy(Batch $batch)
    {
        $this->authorize('delete', $batch);
        $batch->delete();
        return redirect()->route('batches.index')->with('success', 'Batch deleted successfully.');
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
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\GenerateFlashcards;

class CardController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'vocabulary' => 'required|string',
        ]);

        $words = explode("\n", $request->vocabulary);
        $user = $request->user();

        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word)) {
                GenerateFlashcards::dispatch($word, $user);
            }
        }

        return redirect()->route('home')->with('success', 'Your request has been submitted. The flashcards will be generated in the background.');
    }
}
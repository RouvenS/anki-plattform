<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Batch;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!Auth::check()) {
            $totalCards = Cache::remember('total_cards', now()->addMinute(), function () {
                return Card::count();
            });

            return view('welcome', ['totalCards' => $totalCards]);
        }

        $prompts = Prompt::all();
        $batches = Auth::user()->batches()->latest()->paginate(10);
        $userCardCount = Auth::user()->cards()->count();
        $freeCardsTotal = config('trial.free_cards_total');

        return view('home', compact('prompts', 'batches', 'userCardCount', 'freeCardsTotal'));
    }
}
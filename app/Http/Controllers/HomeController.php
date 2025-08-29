<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!Auth::check()) {
            return view('welcome');
        }

        $prompts = Prompt::all();
        $batches = Auth::user()->batches()->latest()->paginate(10);
        return view('home', compact('prompts', 'batches'));
    }
}

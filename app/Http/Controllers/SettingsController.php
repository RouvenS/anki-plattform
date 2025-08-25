<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings', ['user' => Auth::user()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'openai_api_key' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->openai_api_key = $request->openai_api_key;
        $user->save();

        return redirect()->route('settings')->with('success', 'Settings saved successfully.');
    }
}

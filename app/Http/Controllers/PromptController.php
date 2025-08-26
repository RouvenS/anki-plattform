<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    public function index()
    {
        $prompts = auth()->user()->prompts()->orWhere('is_standard', true)->latest()->paginate(10);
        return view('prompts.index', compact('prompts'));
    }

    public function create()
    {
        return view('prompts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'prompt' => 'required|string',
        ]);

        auth()->user()->prompts()->create($request->all());

        return redirect()->route('prompts.index')->with('success', 'Prompt created successfully.');
    }

    public function show(Prompt $prompt)
    {
        $this->authorize('view', $prompt);
        return view('prompts.show', compact('prompt'));
    }

    public function edit(Prompt $prompt)
    {
        $this->authorize('update', $prompt);
        return view('prompts.edit', compact('prompt'));
    }

    public function update(Request $request, Prompt $prompt)
    {
        $this->authorize('update', $prompt);

        $request->validate([
            'name' => 'required|string|max:255',
            'prompt' => 'required|string',
        ]);

        $prompt->update($request->all());

        return redirect()->route('prompts.index')->with('success', 'Prompt updated successfully.');
    }

    public function destroy(Prompt $prompt)
    {
        $this->authorize('delete', $prompt);
        $prompt->delete();
        return redirect()->route('prompts.index')->with('success', 'Prompt deleted successfully.');
    }

    public function duplicate(Prompt $prompt)
    {
        $this->authorize('duplicate', $prompt);

        $newPrompt = $prompt->replicate();
        $newPrompt->name = $prompt->name . ' (copy)';
        $newPrompt->is_standard = false;
        $newPrompt->user_id = auth()->id();
        $newPrompt->save();

        return redirect()->route('prompts.index')->with('success', 'Prompt duplicated successfully.');
    }
}

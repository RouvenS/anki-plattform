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
        return view('batches.show', compact('batch', 'cards'));
    }

    public function destroy(Batch $batch)
    {
        $this->authorize('delete', $batch);
        $batch->delete();
        return redirect()->route('batches.index')->with('success', 'Batch deleted successfully.');
    }
}
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-4xl font-bold text-center mb-8 heading-gradient">Create Prompt</h1>

    <div class="card-glass">
        <form action="{{ route('prompts.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Name</label>
                <input type="text" name="name" id="name" class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40" value="{{ old('name') }}">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="prompt" class="block text-sm font-medium text-slate-700 mb-2">Prompt</label>
                <textarea id="prompt" name="prompt" rows="10" class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40">{{ old('prompt') }}</textarea>
                @error('prompt')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('prompts.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
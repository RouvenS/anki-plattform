@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="relative group">
        <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity duration-300"></div>
        <div class="card-glass">
            <h2 class="text-3xl font-bold text-center mb-8 heading-gradient">Settings</h2>

            @if (session('success'))
                <div class="alert-green mb-6">
                    <div>
                        <p class="font-medium">Success!</p>
                        <p class="text-xs mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('settings.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="openai_api_key" class="block text-sm font-medium text-slate-700 mb-2">OpenAI API Key</label>
                    <input id="openai_api_key" name="openai_api_key" type="password"
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40"
                           placeholder="sk-..."
                           value="{{ $user->openai_api_key }}">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
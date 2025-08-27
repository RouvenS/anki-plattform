@extends('layouts.app')

@section('content')
  {{-- Top hero --}}
  <div class="text-center mb-16">
    <h1 class="text-5xl md:text-6xl font-bold mb-6 heading-gradient">Create Flash Cards</h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
      Transform your vocabulary list into intelligent flash cards.
      Simply paste your words below and we’ll generate definitions, examples, and more.
    </p>
  </div>

  {{-- Glass input card --}}
  <div class="max-w-2xl mx-auto">
    <div class="relative group">
      <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity duration-300"></div>

      <div class="card-glass">
        <form method="POST" action="{{ route('cards.store') }}" class="space-y-6">
          @csrf

          <div>
            <label for="prompt_id" class="block text-sm font-medium text-slate-700 mb-2">Prompt</label>
            <select id="prompt_id" name="prompt_id"
              class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40">
              @foreach($prompts as $prompt)
                <option value="{{ $prompt->id }}">{{ $prompt->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="vocabulary" class="block text-sm font-medium text-slate-700 mb-2">
              Enter your vocabulary words, one per line:
            </label>
            <textarea id="vocabulary" name="vocabulary" rows="4"
              placeholder="car - машина (optional translation, if you want a specific one) 
plane - самолёт
improve - улучшать
try - пытаться"
              class="min-h-64 text-lg w-full rounded-2xl border-0 bg-transparent resize-y
                     focus:ring-0 placeholder:text-slate-400
                     shadow-inner px-3 py-3"></textarea>
          </div>

          @if(Auth::user()?->openai_api_key)
            <div class="flex items-center justify-end">
              <button type="submit" class="btn-primary">
                <span>Generate Cards</span>
                {{-- arrow icon --}}
                <svg class="w-4 h-4 ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12h14" />
                  <path d="M12 5l7 7-7 7" />
                </svg>
              </button>
            </div>
          @else
            <div class="alert-amber">
              {{-- alert icon --}}
              <svg class="w-5 h-5 mt-0.5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 9v4" />
                <path d="M12 17h.01" />
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
              </svg>
              <div>
                <p class="text-sm font-medium">API Key Required</p>
                <p class="text-xs mt-1">
                  Please configure your OpenAI API key in
                  <a href="{{ route('settings') }}" class="underline font-medium">settings</a>
                  to generate flash cards.
                </p>
              </div>
            </div>
          @endif
        </form>
      </div>
    </div>
  </div>
@endsection
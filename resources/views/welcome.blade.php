@extends('layouts.app')

@section('content')
  {{-- Hero Section --}}
  <div class="text-center mb-16">
    <h1 class="text-5xl md:text-6xl font-bold mb-6 heading-gradient">Stop wasting hours making flashcards. Instantly generate perfect Anki decks with translations, audio, and context for any language.</h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
      Focus on learning — let our AI handle the flashcard creation.
    </p>
    <div class="mt-8 flex justify-center items-center space-x-4">
      <a href="{{ route('register') }}" class="btn-primary">
        Get Started
      </a>
      <a href="{{ route('tutorial') }}" class="btn-secondary">
        View Tutorial
      </a>
    </div>
  </div>

  {{-- Social Proof --}}
  <div class="text-center mb-24">
    <p class="text-lg text-slate-600">
      💡 Already <strong>{{ number_format($totalCards) }}+</strong> cards created by early users!
      <br>
      Join now and help shape the smartest vocabulary generator.
    </p>
  </div>

  {{-- How it works Section --}}
  <div class="mt-24">
    <h2 class="text-3xl font-semibold mb-8 text-center">How It Works</h2>
    <div class="grid md:grid-cols-3 gap-8">
      <div class="p-6 bg-white bg-opacity-70 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold mb-4">1️⃣ Import Vocabulary</h3>
        <p class="text-slate-700">
          Add your new words and choose the target language. Press Generate — our AI takes care of the rest.
        </p>
      </div>
      <div class="p-6 bg-white bg-opacity-70 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold mb-4">2️⃣ Generate Flashcards</h3>
        <p class="text-slate-700">
          Automatically create cards with gender hints, example sentences, and native-sounding audio for both the word and sentence.
        </p>
      </div>
      <div class="p-6 bg-white bg-opacity-70 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold mb-4">3️⃣ Start Learning</h3>
        <p class="text-slate-700">
          Review, tweak, and export directly to your Anki deck — your new cards are ready to learn in seconds.
        </p>
      </div>
    </div>
    <p class="text-center text-lg text-slate-600 mt-8">
      💡 It’s that simple: generate → review → export.
    </p>
  </div>

  {{-- CTA Footer --}}
  <div class="text-center mt-24">
    <h2 class="text-3xl font-semibold mb-4">Ready to supercharge your language learning?</h2>
    <p class="text-lg text-slate-600 mb-8">Join now — it’s free during beta.</p>
    <div class="flex justify-center items-center space-x-4">
      <a href="{{ route('register') }}" class="btn-primary">
        Register
      </a>
      <a href="{{ route('login') }}" class="btn-secondary">
        Login
      </a>
    </div>
  </div>

  {{-- Telegram Link --}}
  <div class="text-center mt-12">
    <a href="https://telegram.org" target="_blank" class="inline-flex items-center text-slate-600 hover:text-slate-800">
      <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
        <path d="M9.78 18.65l.28-4.23l7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3L3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.28 1.28.2 1.02.94l-2.54 12.04c-.24.94-.85 1.15-1.6.71l-4.32-3.22-2.05 1.98c-.21.21-.39.39-.7.39z"/>
      </svg>
      👋 Join our early learners community.
    </a>
  </div>
@endsection
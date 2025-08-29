@extends('layouts.app')

@section('content')
  <div class="text-center mb-16">
    <h1 class="text-5xl md:text-6xl font-bold mb-6 heading-gradient">Welcome to Anki-Plattform</h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
      The best way to learn a new language. Create intelligent flash cards from vocabulary lists.
    </p>
  </div>

  <div class="flex justify-center items-center space-x-4">
      <a href="{{ route('login') }}" class="btn-primary">
        Login
      </a>
      <a href="{{ route('register') }}" class="btn-secondary">
        Register
      </a>
  </div>

  {{-- How it works Section --}}
  <div class="mt-24">
    <h2 class="text-3xl font-semibold mb-8 text-center">How It Works</h2>
    <div class="grid md:grid-cols-3 gap-8">
      <div class="p-6 bg-white bg-opacity-70 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold mb-4">1. Import Vocabulary</h3>
        <p class="text-slate-700">
          Start by adding your new vocabulary words. Select the language you want to learn. Press generate and let our AI do the rest!
        </p>
      </div>
      <div class="p-6 bg-white bg-opacity-70 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold mb-4">2. Generate Flash Cards</h3>
        <p class="text-slate-700">
          Our AI-powered system will automatically create flash cards for each word, anontate information like male of female noun, add example sentences, and audio of the word and example sentence pronunciations to enhance your learning experience.
        </p>
      </div>
      <div class="p-6 bg-white bg-opacity-70 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold mb-4">3. Start Learning</h3>
        <p class="text-slate-700">
          Check the results, you can adjust anything you want. When you're satisfied, choose your anki deck and export the flash cards to Anki. Happy learning!
        </p>
      </div>
    </div>
    {{-- Call to Action, view tutorial page to set it up in 5 minutes --}}
    <div class="text-center mt-12">
      <a href="{{ route('tutorial') }}" class="btn-primary btn-lg">
        View Tutorial - Get Started in under 5 Minutes
      </a>

  </div>
@endsection

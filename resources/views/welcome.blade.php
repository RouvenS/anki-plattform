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
@endsection

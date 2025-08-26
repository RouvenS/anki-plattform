@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-center">
        <div class="w-full max-w-lg">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <h1 class="text-2xl font-bold mb-4">Create Flash Cards</h1>
                <p class="mb-4">Transform your vocabulary list into intelligent flash cards. Simply paste your words below and we'll generate definitions, examples, and more.</p>
                <form method="POST" action="{{ route('cards.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="vocabulary">
                            Enter your vocabulary words, one per line:
                        </label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="vocabulary" name="vocabulary" rows="4" placeholder="car - машина (The translation is optional, only if you want a specify it)
plane - самолет
improve - улучшать
try - пытаться"></textarea>
                    </div>
                    @if(Auth::user()->openai_api_key)
                        <div class="flex items-center justify-between">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                                Generate Cards
                            </button>
                        </div>
                    @else
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                            <p class="font-bold">API Key Required</p>
                            <p>Please configure your OpenAI API key in <a href="{{ route('settings') }}" class="font-bold underline">settings</a> to generate flash cards.</p>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

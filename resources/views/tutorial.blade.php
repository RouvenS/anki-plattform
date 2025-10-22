@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-8 heading-gradient">Tutorial</h1>

        <div class="mb-8 card-glass p-6">
            <h2 class="text-2xl font-bold mb-4">In a nutshell</h2>
            <p class="text-lg text-slate-700 mb-4">
                Create your vocabulary anywhere — sync it at home on your desktop with Anki. Our app talks locally to your Anki installation so the cards land straight in your deck.
            </p>
            <p class="text-lg text-red-600 font-semibold">
                Important disclaimer
            </p>
            <p class="text-lg text-slate-700 mb-4">
                To synchronize with Anki, you need a desktop computer (macOS, Windows, or Linux) with the Anki app open.
                You can create vocab on the go, then sync at home with one click.
            </p>
        </div>

        <div class="mb-8 card-glass p-6">
            <h2 class="text-2xl font-bold mb-4">Setup</h2>
            <p class="text-lg text-slate-700 mb-4">
                Goal: Install Anki + AnkiConnect and authorize our website so cards can be added to your deck.
            </p>

            {{-- macOS Section --}}
            <div x-data="{ open: false }" class="border-b border-slate-200 py-4">
                <button @click="open = !open" class="flex justify-between items-center w-full text-xl font-bold text-left text-slate-800 hover:text-violet-600 focus:outline-none">
                    <span>macOS</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse x-cloak x-transition.duration.300ms class="mt-4 text-slate-700">
                    <h4 class="text-lg font-semibold mb-1">Install & launch Anki</h4>
                    <p class="mb-2">Download Anki for macOS, install it, and open Anki.</p>

                    <h4 class="text-lg font-semibold mb-1">Install AnkiConnect</h4>
                    <p class="mb-2">In Anki: Tools → Add-ons → Get Add-ons…, enter code 2055492159 → Install → restart Anki.</p>

                    <h4 class="text-lg font-semibold mb-1">Configure AnkiConnect (allow our website)</h4>
                    <p class="mb-2">In Anki: Tools → Add-ons → AnkiConnect → Config and paste/check the following (origin exactly, no trailing slash):</p>
                    <pre class="bg-slate-100 p-4 rounded-lg text-sm overflow-x-auto"><code>{
  "apiKey": null,
  "apiLogPath": null,
  "ignoreOriginList": [],
  "webBindAddress": "127.0.0.1",
  "webBindPort": 8765,
  "webCorsOriginList": [
    "http://localhost",
    "https://anki-plattform.on-forge.com"
  ]
}</code></pre>
                    <p class="mb-2">Save and restart Anki.</p>

                    <h4 class="text-lg font-semibold mb-1">Test</h4>
                    <p class="mb-2">Back on our website: select cards → click “Add to Anki.” You should see a confirmation.</p>
                </div>
            </div>

            {{-- Windows Section --}}
            <div x-data="{ open: false }" class="border-b border-slate-200 py-4">
                <button @click="open = !open" class="flex justify-between items-center w-full text-xl font-bold text-left text-slate-800 hover:text-violet-600 focus:outline-none">
                    <span>Windows</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse x-cloak x-transition.duration.300ms class="mt-4 text-slate-700">
                    <h4 class="text-lg font-semibold mb-1">Install & launch Anki</h4>
                    <p class="mb-2">Download Anki for Windows, install it, and open Anki.</p>

                    <h4 class="text-lg font-semibold mb-1">Install AnkiConnect</h4>
                    <p class="mb-2">Tools → Add-ons → Get Add-ons… → code 2055492159 → Install → restart Anki.</p>

                    <h4 class="text-lg font-semibold mb-1">Configure AnkiConnect</h4>
                    <p class="mb-2">Tools → Add-ons → AnkiConnect → Config and use:</p>
                    <pre class="bg-slate-100 p-4 rounded-lg text-sm overflow-x-auto"><code>{
  "apiKey": null,
  "apiLogPath": null,
  "ignoreOriginList": [],
  "webBindAddress": "127.0.0.1",
  "webBindPort": 8765,
  "webCorsOriginList": [
    "http://localhost",
    "https://anki-plattform.on-forge.com"
  ]
}</code></pre>
                    <p class="mb-2">Save and restart Anki.</p>

                    <h4 class="text-lg font-semibold mb-1">Test</h4>
                    <p class="mb-2">On the website, select cards → “Add to Anki.”</p>
                </div>
            </div>

            {{-- Linux Section --}}
            <div x-data="{ open: false }" class="border-b border-slate-200 py-4">
                <button @click="open = !open" class="flex justify-between items-center w-full text-xl font-bold text-left text-slate-800 hover:text-violet-600 focus:outline-none">
                    <span>Linux</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse x-cloak x-transition.duration.300ms class="mt-4 text-slate-700">
                    <h4 class="text-lg font-semibold mb-1">Install & launch Anki</h4>
                    <p class="mb-2">Install Anki from the official source/package and open Anki.</p>

                    <h4 class="text-lg font-semibold mb-1">Install AnkiConnect</h4>
                    <p class="mb-2">Tools → Add-ons → Get Add-ons… → code 2055492159 → Install → restart Anki.</p>

                    <h4 class="text-lg font-semibold mb-1">Configure AnkiConnect</h4>
                    <p class="mb-2">Tools → Add-ons → AnkiConnect → Config and set:</p>
                    <pre class="bg-slate-100 p-4 rounded-lg text-sm overflow-x-auto"><code>{
  "apiKey": null,
  "apiLogPath": null,
  "ignoreOriginList": [],
  "webBindAddress": "127.0.0.1",
  "webBindPort": 8765,
  "webCorsOriginList": [
    "http://localhost",
    "https://anki-plattform.on-forge.com"
  ]
}</code></pre>
                    <p class="mb-2">Save and restart Anki.</p>

                    <h4 class="text-lg font-semibold mb-1">Test</h4>
                    <p class="mb-2">On the website, select cards → “Add to Anki.”</p>
                </div>
            </div>

            <p class="text-lg text-slate-700 mt-4">
                <span class="font-semibold">Tip:</span> If you change your domain later, add the new HTTPS origin to webCorsOriginList (exact origin, no trailing slash).
            </p>
        </div>

        <div class="mb-8 card-glass p-6">
            <h2 class="text-2xl font-bold mb-4">How it works (one sentence)</h2>
            <p class="text-lg text-slate-700 mb-4">
                Your browser connects locally to AnkiConnect at http://127.0.0.1:8765. We only send the notes—nothing leaves your computer.
            </p>
        </div>

        <div class="mb-8 card-glass p-6">
            <h2 class="text-2xl font-bold mb-4">Troubleshooting</h2>
            <h3 class="text-xl font-bold mb-2">Is Anki open?</h3>
            <p class="text-slate-700 mb-4">
                AnkiConnect only works when Anki is running. Open Anki and try again.
            </p>

            <h3 class="text-xl font-bold mb-2">Privacy/Ad blocker in the way?</h3>
            <p class="text-lg text-slate-700 mb-4">
                Some protections (e.g., Brave Shields, certain ad/privacy blockers) can block local connections to 127.0.0.1.
                Fix: Allowlist this website (or temporarily disable the blocker for this site) and try again.
            </p>
        </div>
    </div>
@endsection

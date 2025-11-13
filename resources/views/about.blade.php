@extends('layouts.app')

@section('content')
  <div class="text-center mb-16">
    <h1 class="text-5xl md:text-6xl font-bold mb-6 heading-gradient">About Anki-Plattform</h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
      We are dedicated to making language learning more effective and engaging through intelligent flash cards.
    </p>
  </div>

  <div class="max-w-4xl mx-auto space-y-8 text-slate-700">
    <div class="p-8 rounded-2xl bg-white/50 border border-white/30 shadow-lg">
        <h2 class="text-2xl font-bold mb-4">Our Mission</h2>
        <p class="leading-relaxed">
            In a world that's more connected than ever, language is the key to unlocking new cultures, friendships, and opportunities. Our mission is to provide language learners with a powerful, easy-to-use tool that leverages cutting-edge AI to create personalized and effective learning experiences. We believe that by automating the tedious parts of vocabulary acquisition, we can help you focus on what truly matters: mastering a new language - keywords are important.
        </p>
    </div>

    <div class="p-8 rounded-2xl bg-white/50 border border-white/30 shadow-lg">
        <h2 class="text-2xl font-bold mb-4">The Technology</h2>
        <p class="leading-relaxed mb-4">
            Anki-Plattform is built on a modern tech stack to deliver a fast, reliable, and seamless experience.
        </p>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>Backend:</strong> Laravel 12 provides the robust foundation for our application logic and API.</li>
            <li><strong>Frontend:</strong> We use Blade templates with Tailwind CSS for a utility-first approach to styling, ensuring a clean and responsive design.</li>
            <li><strong>AI Integration:</strong> Card generation is powered by OpenAI's powerful language models, allowing us to provide definitions, example sentences, and more.</li>
            <li><strong>Text-to-Speech:</strong> Audio pronunciations are generated using AI to help with listening and speaking skills.</li>
        </ul>
    </div>

    <div class="p-8 rounded-2xl bg-white/50 border border-white/30 shadow-lg">
        <h2 class="text-2xl font-bold mb-4">Our Story</h2>
        <p class="leading-relaxed">
            This project started as a simple idea: what if creating flash cards could be as easy as pasting a list of words? As language learners ourselves, we were tired of the manual effort required to build effective study materials. We decided to build the tool we always wanted, and Anki-Plattform is the result. We are passionate about open-source and building tools that help people learn and grow.
        </p>
    </div>
  </div>
@endsection
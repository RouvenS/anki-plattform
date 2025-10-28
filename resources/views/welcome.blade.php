@extends('layouts.app')

@section('content')
  {{-- Hero Section --}}
  <div class="text-center mb-8">
    <div class="my-8 flex justify-center items-center space-x-4 text-4xl md:text-5xl">
        <div id="visitor-flag" role="img" aria-label="Visitor flag">🇩🇪</div>

        <div role="img" aria-label="Right arrow">👉</div>

        {{-- Target flag (JS controls animations) --}}
        <div id="target-language-flag" role="img" aria-label="Rotating flags" class="inline-block">🇬🇧</div>
    </div>

    <h1 class="text-4xl md:text-5xl font-bold mb-6 heading-gradient pb-2">
        Instantly generate perfect Anki decks with translations, audio, and context for any language.
    </h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed mt-6">
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

    {{-- Social Proof --}}
    <div class="mt-8">
        <p class="text-lg text-slate-600">
            💡 <strong>{{ number_format($totalCards) }}+</strong> cards already created by early users.
            Join now and help shape the smartest vocab generator.
        </p>
    </div>
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

  <div class="text-center mt-12">
    <a href="https://telegram.org" target="_blank" class="btn-grey">
      <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
        <path d="M9.78 18.65l.28-4.23l7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3L3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.28 1.28.2 1.02.94l-2.54 12.04c-.24.94-.85 1.15-1.6.71l-4.32-3.22-2.05 1.98c-.21.21-.39.39-.7.39z"/>
      </svg>
      Get Support on Telegram
    </a>
  </div>
@endsection

@push('scripts')
<style>
  /* Keep your reliable custom in/out so it works even if Animate.css isn't loaded yet */
  @keyframes backOutDown {
      0%   { transform: scale(1); opacity: 1; }
      20%  { transform: translateY(0) scale(0.7); opacity: 0.7; }
      100% { transform: translateY(700px) scale(0.7); opacity: 0.7; }
  }
  @keyframes backInDown {
      0%   { transform: translateY(-700px) scale(0.7); opacity: 0.7; }
      80%  { transform: translateY(0)      scale(0.7); opacity: 0.7; }
      100% { transform: scale(1); opacity: 1; }
  }
  .animate-back-out-down { animation: backOutDown 0.9s forwards; }
  .animate-back-in-down  { animation: backInDown  0.9s forwards; }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Ensure Animate.css is available (safe to inject even if already present)
    (function ensureAnimateCSS(){
      const has = Array.from(document.styleSheets).some(s => (s.href||'').includes('animate.min.css'));
      if (!has) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
        link.referrerPolicy = 'no-referrer';
        document.head.appendChild(link);
      }
    })();

    const target = document.getElementById('target-language-flag');
    target.style.display = 'inline-block'; // don't animate inline elements

    const flags = ['🇬🇧','🇫🇷','🇪🇸','🇮🇹','🇵🇱','🇺🇦','🇵🇹','🇷🇺','🇯🇵','🇨🇳','🇰🇷'];

    // attention-seekers (randomized, no immediate repeats)
    const seekers = ['bounce','flash','pulse','rubberBand','shakeX','shakeY','headShake','swing','tada','wobble','jello','heartBeat'];

    // Visitor flag from browser language
    const languageToFlag = {
      'en':'🇺🇸','en-US':'🇺🇸','en-GB':'🇬🇧',
      'de':'🇩🇪','fr':'🇫🇷','es':'🇪🇸','it':'🇮🇹',
      'pl':'🇵🇱','uk':'🇺🇦','uk-UA':'🇺🇦','pt':'🇵🇹','pt-PT':'🇵🇹',
      'ru':'🇷🇺','ja':'🇯🇵','zh':'🇨🇳','zh-CN':'🇨🇳','zh-TW':'🇹🇼','ko':'🇰🇷'
    };
    const visitorFlag = document.getElementById('visitor-flag');
    const userLang = (navigator.language || navigator.userLanguage || 'de').trim();
    visitorFlag.textContent = languageToFlag[userLang] || languageToFlag[userLang.split('-')[0]] || '🇩🇪';

    const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // helper: animate with given classes, then clean up
    function animateOnce(el, classes) {
      return new Promise(resolve => {
        const toAdd = Array.isArray(classes) ? classes : [classes];
        // force reflow so re-adding classes retriggers
        void el.offsetWidth;
        el.classList.add(...toAdd);
        function onEnd(e){
          e.stopPropagation();
          el.classList.remove(...toAdd);
          el.removeEventListener('animationend', onEnd);
          resolve();
        }
        el.addEventListener('animationend', onEnd, { once:true });
      });
    }

    function pickNextDifferent(list, prev) {
      if (!prev) return list[Math.floor(Math.random()*list.length)];
      let c = prev;
      while (c === prev) c = list[Math.floor(Math.random()*list.length)];
      return c;
    }

    let idx = 0;
    let lastSeeker = null;
    const dwellMs = 1000;

    async function cycle() {
      if (reduced) {
        idx = (idx + 1) % flags.length;
        target.textContent = flags[idx];
        setTimeout(cycle, dwellMs);
        return;
      }

      // EXIT (custom, reliable)
      await animateOnce(target, 'animate-back-out-down');

      // SWAP flag
      idx = (idx + 1) % flags.length;
      target.textContent = flags[idx];

      // ENTER (custom, reliable)
      await animateOnce(target, 'animate-back-in-down');

      // ATTENTION (Animate.css)
      const seeker = pickNextDifferent(seekers, lastSeeker);
      lastSeeker = seeker;
      await animateOnce(target, ['animate__animated', `animate__${seeker}`, 'animate__faster']);

      setTimeout(cycle, dwellMs);
    }

    // initial enter + attention so it feels alive on first paint
    (async () => {
      if (!reduced) {
        await animateOnce(target, 'animate-back-in-down');
        const firstSeeker = pickNextDifferent(seekers, null);
        lastSeeker = firstSeeker;
        await animateOnce(target, ['animate__animated', `animate__${firstSeeker}`, 'animate__faster']);
      }
      setTimeout(cycle, dwellMs);
    })();
  });
</script>
@endpush

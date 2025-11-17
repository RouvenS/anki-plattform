@extends('layouts.app')

@section('content')
  {{-- Hero Section --}}
  <div class="text-center mb-8">
    <h1 class="text-4xl md:text-5xl font-bold mb-6 heading-gradient pb-2">
        Instantly generate perfect Anki decks with translations, audio, and context for any language.
    </h1>

    <div class="my-8 flex justify-center items-center space-x-4 text-4xl md:text-5xl">
        <div id="visitor-flag" role="img" aria-label="Visitor flag"></div>

        <div id="arrow-emoji" role="img" aria-label="Right arrow"></div>

        {{-- Target flag (JS controls animations) --}}
        <div id="target-language-flag" role="img" aria-label="Rotating flags" class="inline-block"></div>
    </div>

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
            <span id="bulb-emoji-1"></span> <strong>{{ number_format($totalCards) }}+</strong> cards already created by early users.
            Join now and help shape the smartest vocab generator.
        </p>
    </div>
  </div>

  {{-- How it works Section --}}
  <div class="mt-24">
    <h2 class="text-3xl font-semibold mb-8 text-center">How It Works</h2>
    <div class="grid md:grid-cols-3 gap-8">
      <div class="p-6 bg-white bg-opacity-70 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold mb-4"><span id="emoji-1"></span> Import Vocabulary</h3>
        <p class="text-slate-700">
          Add your new words and choose the target language. Press Generate — our AI takes care of the rest.
        </p>
      </div>
      <div class="p-6 bg-white bg-opacity-70 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold mb-4"><span id="emoji-2"></span> Generate Flashcards</h3>
        <p class="text-slate-700">
          Automatically create cards with gender hints, example sentences, and native-sounding audio for both the word and sentence.
        </p>
      </div>
      <div class="p-6 bg-white bg-opacity-70 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold mb-4"><span id="emoji-3"></span> Start Learning</h3>
        <p class="text-slate-700">
          Review, tweak, and export directly to your Anki deck — your new cards are ready to learn in seconds.
        </p>
      </div>
    </div>
    <p class="text-center text-lg text-slate-600 mt-8">
      <span id="bulb-emoji-2"></span> It’s that simple: generate → review → export.
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
  .animate-back-out-down { animation: backOutDown 1.2s forwards; }
  .animate-back-in-down  { animation: backInDown  1.2s forwards; }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const isWindows = navigator.platform.indexOf('Win') > -1;

    function toTwemojiCodepoint(emoji) {
        if (!emoji) return '';
        // Twemoji codepoint logic needs to strip variation selectors like U+FE0F for compatibility.
        return [...emoji]
            .filter(char => char.codePointAt(0) !== 0xFE0F)
            .map(char => char.codePointAt(0).toString(16))
            .join('-');
    }

    function setEmoji(element, emoji, options = {}) {
        if (!element) return;

        const {
            sizeClass = 'h-12 w-12', // Default for flags
            alt = emoji,
            title = emoji
        } = options;

        if (isWindows) {
            element.innerHTML = `<img src="https://twemoji.maxcdn.com/v/latest/svg/${toTwemojiCodepoint(emoji)}.svg" 
                                      alt="${alt}" title="${title}" 
                                      class="inline-block align-middle ${sizeClass}">`;
        } else {
            element.textContent = emoji;
        }
    }

    // --- Static Emojis ---
    setEmoji(document.getElementById('arrow-emoji'), '👉', { sizeClass: 'h-12 w-12 md:h-14 md:w-14' });
    setEmoji(document.getElementById('bulb-emoji-1'), '💡', { sizeClass: 'h-6 w-6 mr-1' });
    setEmoji(document.getElementById('bulb-emoji-2'), '💡', { sizeClass: 'h-6 w-6 mr-1' });
    setEmoji(document.getElementById('emoji-1'), '1️⃣', { sizeClass: 'h-7 w-7 mr-2' });
    setEmoji(document.getElementById('emoji-2'), '2️⃣', { sizeClass: 'h-7 w-7 mr-2' });
    setEmoji(document.getElementById('emoji-3'), '3️⃣', { sizeClass: 'h-7 w-7 mr-2' });

    // --- Dynamic Flags ---
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
    target.style.display = 'inline-block';

    const flags = ['🇬🇧','🇫🇷','🇪🇸','🇮🇹','🇵🇱','🇺🇦','🇵🇹','🇷🇺','🇯🇵','🇨🇳','🇰🇷'];
    const seekers = ['bounce','flash','pulse','rubberBand','shakeX','shakeY','headShake','swing','tada','wobble','jello','heartBeat'];

    const languageToFlag = {
      'en':'🇺🇸','en-US':'🇺🇸','en-GB':'🇬🇧',
      'de':'🇩🇪','fr':'🇫🇷','es':'🇪🇸','it':'🇮🇹',
      'pl':'🇵🇱','uk':'🇺🇦','uk-UA':'🇺🇦','pt':'🇵🇹','pt-PT':'🇵🇹',
      'ru':'🇷🇺','ja':'🇯🇵','zh':'🇨🇳','zh-CN':'🇨🇳','zh-TW':'🇹🇼','ko':'🇰🇷'
    };
    const visitorFlag = document.getElementById('visitor-flag');
    const userLang = (navigator.language || navigator.userLanguage || 'de').trim();
    const visitorEmoji = languageToFlag[userLang] || languageToFlag[userLang.split('-')[0]] || '🇩🇪';
    setEmoji(visitorFlag, visitorEmoji, { sizeClass: 'h-12 w-12 md:h-14 md:w-14' });

    const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function animateOnce(el, classes) {
      return new Promise(resolve => {
        const toAdd = Array.isArray(classes) ? classes : [classes];
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
    const dwellMs = 1100;

    async function cycle() {
      if (reduced) {
        idx = (idx + 1) % flags.length;
        setEmoji(target, flags[idx], { sizeClass: 'h-12 w-12 md:h-14 md:w-14' });
        setTimeout(cycle, dwellMs);
        return;
      }

      await animateOnce(target, 'animate-back-out-down');
      idx = (idx + 1) % flags.length;
      setEmoji(target, flags[idx], { sizeClass: 'h-12 w-12 md:h-14 md:w-14' });
      await animateOnce(target, 'animate-back-in-down');

      const seeker = pickNextDifferent(seekers, lastSeeker);
      lastSeeker = seeker;
      await animateOnce(target, ['animate__animated', `animate__${seeker}`]);

      setTimeout(cycle, dwellMs);
    }

    (async () => {
      const initialFlag = flags[0]; // Start with the first flag in the list
      setEmoji(target, initialFlag, { sizeClass: 'h-12 w-12 md:h-14 md:w-14' });

      if (!reduced) {
        await animateOnce(target, 'animate-back-in-down');
        const firstSeeker = pickNextDifferent(seekers, null);
        lastSeeker = firstSeeker;
        await animateOnce(target, ['animate__animated', `animate__${firstSeeker}`]);
      }
      setTimeout(cycle, dwellMs);
    })();
  });
</script>
@endpush

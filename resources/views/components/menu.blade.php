<nav class="nav-container">
<div class="flex items-center justify-between">
    <a href="{{ url('/') }}" class="flex items-center gap-3 group transition-all duration-300">
    <div class="w-10 h-10 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl grid place-items-center group-hover:scale-105 transition-transform duration-300">
        {{-- book icon (lucide-like) --}}
        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
        <path d="M4 4v15.5" />
        <path d="M8 4v13" />
        <path d="M16 4v13" />
        <path d="M20 4v13" />
        </svg>
    </div>
    <span class="brand">AnkiCraft</span>
    </a>

    <div class="flex items-center gap-8">
    <a href="{{ url('/') }}" class="text-sm font-medium transition-all duration-300 hover:text-violet-600 {{ request()->is('/') ? 'text-violet-600' : 'text-slate-600' }}">Home</a>
    <a href="{{ url('/about') }}" class="text-sm font-medium transition-all duration-300 hover:text-violet-600 {{ request()->is('about') ? 'text-violet-600' : 'text-slate-600' }}">About</a>

    @guest
        <a href="{{ route('login') }}" class="text-sm font-medium transition-all duration-300 hover:text-violet-600 text-slate-600">Login</a>
        <a href="{{ route('register') }}" class="text-sm font-medium transition-all duration-300 hover:text-violet-600 text-slate-600">Register</a>
    @endguest
    @auth
        <a href="{{ route('settings') }}" class="text-sm font-medium transition-all duration-300 hover:text-violet-600 text-slate-600">Settings</a>
        <a href="{{ route('batches.index') }}" class="text-sm font-medium transition-all duration-300 hover:text-violet-600 text-slate-600">Batches</a>
        <a href="{{ route('prompts.index') }}" class="text-sm font-medium transition-all duration-300 hover:text-violet-600 text-slate-600">Prompts</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button class="text-sm font-medium transition-all duration-300 hover:text-violet-600 text-slate-600">Logout</button>
        </form>
    @endauth
    </div>
</div>
</nav>
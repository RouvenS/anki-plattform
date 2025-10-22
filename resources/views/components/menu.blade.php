<div class="nav-container">
    <div class="flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group transition-all duration-300">
            <div class="w-10 h-10 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl grid place-items-center group-hover:scale-105 transition-transform duration-300">
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M4 4v15.5" />
                    <path d="M8 4v13" />
                    <path d="M16 4v13" />
                    <path d="M20 4v13" />
                </svg>
            </div>
            <span class="brand">Anki-Plattform</span>
        </a>

        <!-- Hamburger Menu Button (visible on mobile) -->
        <div class="md:hidden">
            <button id="menu-toggle" class="text-slate-600 hover:text-violet-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav id="menu-links" class="hidden md:flex items-center gap-8">
            @auth
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('prompts.index') }}" class="nav-link {{ request()->routeIs('prompts.index') ? 'active' : '' }}">Prompts</a>
                <a href="{{ route('tutorial') }}" class="nav-link {{ request()->routeIs('tutorial') ? 'active' : '' }}">Tutorial</a>
                <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">Settings</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="nav-link">Logout</button>
                </form>
            @else
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                <a href="{{ route('tutorial') }}" class="nav-link {{ request()->routeIs('tutorial') ? 'active' : '' }}">Tutorial</a>
                <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                <a href="{{ route('register') }}" class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}">Register</a>
            @endauth
        </nav>
    </div>
</div>

<script>
    document.getElementById('menu-toggle').addEventListener('click', function() {
        var menu = document.getElementById('menu-links');
        menu.classList.toggle('hidden');
        menu.classList.toggle('mobile-menu');
    });
</script>

@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="relative group">
        <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity duration-300"></div>
        <div class="card-glass">
            <h2 class="text-3xl font-bold text-center mb-8 heading-gradient">Create your Account</h2>

            @if ($errors->any())
                <div class="alert-amber mb-6">
                    <div>
                        <p class="font-medium">Something went wrong</p>
                        <ul class="mt-1.5 list-disc list-inside text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Name</label>
                    <input id="name" name="name" type="text" autocomplete="name" required
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40"
                           placeholder="Your Name">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40"
                           placeholder="you@example.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40"
                           placeholder="••••••••">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40"
                           placeholder="••••••••">
                </div>

                <div>
                    <button type="submit" class="w-full btn-primary">
                        Create Account
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-slate-600 mt-8">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-violet-600 hover:underline">Log in</a>
            </p>
        </div>
    </div>
</div>
@endsection

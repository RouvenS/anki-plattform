@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="relative group">
        <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity duration-300"></div>
        <div class="card-glass text-center p-12">
            <div class="w-20 h-20 bg-violet-100 text-violet-600 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <h1 class="text-4xl font-bold mb-6 heading-gradient">Email Confirmed</h1>

            <p class="text-slate-600 mb-10 text-xl leading-relaxed">
                Email confirmed successfully.<br>
                You are now eligible to use your free credits.
            </p>

            <div class="flex justify-center">
                <a href="{{ route('home') }}" class="btn-primary">
                    Go to Dashboard
                    <svg class="w-5 h-5 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

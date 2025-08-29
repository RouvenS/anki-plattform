@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-4xl font-bold text-center mb-8 heading-gradient">{{ $prompt->name }}</h1>

    <div class="card-glass">
        <dl class="divide-y divide-slate-200">
            <div class="py-4 sm:py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-slate-500">Name</dt>
                <dd class="text-sm text-slate-900 col-span-2">{{ $prompt->name }}</dd>
            </div>
            <div class="py-4 sm:py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-slate-500">Type</dt>
                <dd class="text-sm text-slate-900 col-span-2">
                    @if($prompt->is_standard)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Standard
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-violet-100 text-violet-800">
                            Custom
                        </span>
                    @endif
                </dd>
            </div>
            <div class="py-4 sm:py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-slate-500">Prompt</dt>
                <dd class="text-sm text-slate-900 col-span-2">
                    <pre class="whitespace-pre-wrap font-sans">{{ $prompt->prompt }}</pre>
                </dd>
            </div>
        </dl>
    </div>
    <div class="mt-8 text-center">
        <a href="{{ route('prompts.index') }}" class="btn-secondary">Back to all prompts</a>
    </div>
</div>
@endsection
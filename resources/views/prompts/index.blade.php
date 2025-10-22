@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold heading-gradient">My Prompts</h1>
        <a href="{{ route('prompts.create') }}" class="btn-primary">Create Prompt</a>
    </div>

    @if (session('success'))
        <div class="alert-green mb-6">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="card-glass">
        <!-- Mobile view: Card list -->
        <div class="md:hidden">
            <div class="divide-y divide-slate-200">
                @foreach ($prompts as $prompt)
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="text-sm text-slate-900 font-medium">{{ $prompt->name }}</div>
                            @if($prompt->is_standard)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Standard</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-violet-100 text-violet-800">Custom</span>
                            @endif
                        </div>
                        <div class="mt-3 flex justify-end flex-wrap gap-2">
                            <a href="{{ route('prompts.show', $prompt) }}" class="btn-secondary">View</a>
                            @can('update', $prompt)
                                <a href="{{ route('prompts.edit', $prompt) }}" class="btn-icon btn-icon-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z" /></svg>
                                </a>
                            @endcan
                            @can('duplicate', $prompt)
                                <form class="inline-block" action="{{ route('prompts.duplicate', $prompt) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-icon btn-icon-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    </button>
                                </form>
                            @endcan
                            @can('delete', $prompt)
                                <form class="inline-block" action="{{ route('prompts.destroy', $prompt) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-icon-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Desktop view: Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-slate-500">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-slate-500">Type</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($prompts as $prompt)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $prompt->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($prompt->is_standard)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Standard</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-violet-100 text-violet-800">Custom</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('prompts.show', $prompt) }}" class="btn-secondary">View</a>
                                    @can('update', $prompt)
                                        <a href="{{ route('prompts.edit', $prompt) }}" class="btn-icon btn-icon-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z" /></svg>
                                        </a>
                                    @endcan
                                    @can('duplicate', $prompt)
                                        <form class="inline-block" action="{{ route('prompts.duplicate', $prompt) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-icon btn-icon-success">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                            </button>
                                        </form>
                                    @endcan
                                    @can('delete', $prompt)
                                        <form class="inline-block" action="{{ route('prompts.destroy', $prompt) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-icon-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-4 md:px-0">
            {{ $prompts->links() }}
        </div>
    </div>
</div>
@endsection

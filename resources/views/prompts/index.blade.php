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
        <div class="overflow-x-auto">
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
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Standard
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-violet-100 text-violet-800">
                                        Custom
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('prompts.show', $prompt) }}" class="btn-secondary">View</a>
                                @can('update', $prompt)
                                    <a href="{{ route('prompts.edit', $prompt) }}" class="btn-secondary">Edit</a>
                                @endcan
                                @can('duplicate', $prompt)
                                    <form class="inline-block" action="{{ route('prompts.duplicate', $prompt) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-success">Duplicate</button>
                                    </form>
                                @endcan
                                @can('delete', $prompt)
                                    <form class="inline-block" action="{{ route('prompts.destroy', $prompt) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $prompts->links() }}
        </div>
    </div>
</div>
@endsection
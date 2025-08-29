@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-4xl font-bold text-center mb-8 heading-gradient">My Batches</h1>

    @if (session('success'))
        <div class="alert-green mb-6">
            <div>
                <p class="font-medium">Success!</p>
                <p class="text-xs mt-1">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    <div id="error-message" class="hidden alert-amber mb-6"></div>

    <div class="card-glass">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-slate-500">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-slate-500">
                            Created At
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($batches as $batch)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">
                                    <span class="editable-batch-name" data-batch-id="{{ $batch->id }}" contenteditable="true">{{ $batch->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ $batch->created_at->format('Y-m-d H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('batches.show', $batch) }}" class="btn-secondary">View</a>
                                <form class="inline-block" action="{{ route('batches.destroy', $batch) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $batches->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.editable-batch-name').forEach(item => {
        item.addEventListener('blur', function(e) {
            updateBatchName(e.target);
        });
        item.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.target.blur();
            }
        });
    });

    function updateBatchName(element) {
        const batchId = element.dataset.batchId;
        const newName = element.innerText;
        const originalName = element.dataset.originalName || newName;

        fetch(`/batches/${batchId}`,
        {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: newName })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'An unknown error occurred.');
                });
            }
            return response.json();
        })
        .then(data => {
            if(data.success) {
                element.dataset.originalName = newName;
                // Maybe show a success message
            }
        })
        .catch(error => {
            console.error('Error:', error);
            element.innerText = originalName; // Revert to original name
            const errorMessage = document.getElementById('error-message');
            errorMessage.innerText = `Error updating batch name: ${error.message}`;
            errorMessage.classList.remove('hidden');
            setTimeout(() => {
                errorMessage.classList.add('hidden');
            }, 5000);
        });
    }
</script>
@endpush
@endsection
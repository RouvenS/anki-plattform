@extends('layouts.app')

@section('content')
  {{-- Top hero --}}
  <div class="text-center mb-16">
    <h1 class="text-5xl md:text-6xl font-bold mb-6 heading-gradient">Create Flash Cards</h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
      Transform your vocabulary list into intelligent flash cards.
      Simply paste your words below and we’ll generate definitions, examples, and more.
    </p>
    <div class="mt-8">
        <button id="scroll-down-btn" class="btn-secondary">
            See your created cards
            <svg class="w-4 h-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
            </svg>
        </button>
    </div>
  </div>

  {{-- Glass input card --}}
  <div class="max-w-2xl mx-auto">
    <div class="relative group">
      <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity duration-300"></div>

      <div class="card-glass">
        <form method="POST" action="{{ route('cards.store') }}" class="space-y-6">
          @csrf

          <div>
            <label for="prompt_id" class="block text-sm font-medium text-slate-700 mb-2">Prompt</label>
            <select id="prompt_id" name="prompt_id"
              class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40">
              @foreach($prompts as $prompt)
                <option value="{{ $prompt->id }}">{{ $prompt->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label for="vocabulary" class="block text-sm font-medium text-slate-700 mb-2">
              Enter your vocabulary words, one per line:
            </label>
            <textarea id="vocabulary" name="vocabulary" rows="4"
              placeholder="car - машина (optional translation, if you want a specific one) 
plane - самолёт
improve - улучшать
try - пытаться"
              class="min-h-48 text-lg w-full rounded-2xl border-0 bg-transparent resize-y
                     focus:ring-0 placeholder:text-slate-400
                     shadow-inner px-3 py-3"></textarea>
          </div>

          @if(Auth::user()?->openai_api_key)
            <div class="flex items-center justify-end">
              <button type="submit" class="btn-primary">
                <span>Generate Cards</span>
                {{-- arrow icon --}}
                <svg class="w-4 h-4 ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12h14" />
                  <path d="M12 5l7 7-7 7" />
                </svg>
              </button>
            </div>
          @else
            <div class="alert-amber">
              {{-- alert icon --}}
              <svg class="w-5 h-5 mt-0.5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 9v4" />
                <path d="M12 17h.01" />
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
              </svg>
              <div>
                <p class="text-sm font-medium">API Key Required</p>
                <p class="text-xs mt-1">
                  Please configure your OpenAI API key in
                  <a href="{{ route('settings') }}" class="underline font-medium">settings</a>
                  to generate flash cards.
                </p>
              </div>
            </div>
          @endif
        </form>
      </div>
    </div>
  </div>

  {{-- Batches List --}}
  <div class="max-w-4xl mx-auto mt-16">
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
        <!-- Mobile view: Card list -->
        <div class="md:hidden">
            <div class="divide-y divide-slate-200">
                @foreach ($batches as $batch)
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="text-sm text-slate-900 font-medium">
                                <span class="editable-batch-name" data-batch-id="{{ $batch->id }}" contenteditable="true">{{ $batch->name }}</span>
                            </div>
                            <div class="text-xs text-slate-500">{{ $batch->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="mt-3 flex justify-end space-x-2">
                            <a href="{{ route('batches.show', $batch) }}" class="btn-secondary">View</a>
                            <form class="inline-block" action="{{ route('batches.destroy', $batch) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
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
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-slate-500">Created At</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
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

        <div class="mt-4 px-4 md:px-0">
            {{ $batches->links() }}
        </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('scroll-down-btn').addEventListener('click', () => {
        window.scrollBy({
            top: window.innerHeight / 2,
            left: 0,
            behavior: 'smooth'
        });
    });

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

    const promptSelect = document.getElementById('prompt_id');
    if (promptSelect) {
        const savedPrompt = localStorage.getItem('selectedPrompt');
        if (savedPrompt) {
            if (promptSelect.querySelector(`option[value="${savedPrompt}"]`)) {
                promptSelect.value = savedPrompt;
            }
        }
        promptSelect.addEventListener('change', function() {
            localStorage.setItem('selectedPrompt', this.value);
        });
    }
});
</script>
@endpush

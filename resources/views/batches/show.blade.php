@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">{{ $batch->name }}</h1>

    <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    </div>
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('cards.add-to-anki') }}" method="POST">
        @csrf
        <input type="hidden" name="batch_id" value="{{ $batch->id }}">
        <div class="flex justify-between items-center mb-4">
            <div>
                <label for="deck" class="block text-sm font-medium text-gray-700">Anki Deck</label>
                <select id="deck" name="deck" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    @if(!empty($decks))
                        @foreach ($decks as $deck)
                            <option>{{ $deck }}</option>
                        @endforeach
                    @else
                        <option disabled>Could not connect to Anki. Is it running?</option>
                    @endif
                </select>
            </div>
            <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" @if(empty($decks)) disabled @endif>
                Add to Anki
            </button>
        </div>

        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Front
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Back
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        TTS
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Audio
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($cards as $card)
                                    <tr data-card-id="{{ $card->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="cards[]" value="{{ $card->id }}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 card-checkbox">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900 editable" data-field="front" contenteditable="true">{{ $card->front }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900 editable" data-field="back" contenteditable="true">{{ $card->back }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900 editable" data-field="tts" contenteditable="true">{{ $card->tts }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($card->audio_path)
                                                <audio controls src="{{ Storage::url($card->audio_path) }}"></audio>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button type="button" class="hidden save-btn text-indigo-600 hover:text-indigo-900">Save</button>
                                            <button type="button" class="hidden cancel-btn text-red-600 hover:text-red-900 ml-4">Cancel</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $cards->links() }}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('select-all').addEventListener('click', function(event) {
        document.querySelectorAll('.card-checkbox').forEach(function(checkbox) {
            checkbox.checked = event.target.checked;
        });
    });

    document.querySelectorAll('.editable').forEach(item => {
        item.dataset.originalValue = item.innerText;
        item.addEventListener('input', function(e) {
            const row = e.target.closest('tr');
            row.querySelector('.save-btn').classList.remove('hidden');
            row.querySelector('.cancel-btn').classList.remove('hidden');
        });
    });

    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const row = e.target.closest('tr');
            const cardId = row.dataset.cardId;
            const front = row.querySelector('[data-field="front"]').innerText;
            const back = row.querySelector('[data-field="back"]').innerText;
            const tts = row.querySelector('[data-field="tts"]').innerText;

            fetch(`/cards/${cardId}`,
            {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ front, back, tts })
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
                    row.querySelector('.save-btn').classList.add('hidden');
                    row.querySelector('.cancel-btn').classList.add('hidden');
                    row.querySelectorAll('.editable').forEach(item => {
                        item.dataset.originalValue = item.innerText;
                    });
                    if (data.audio_path) {
                        const audioPlayer = row.querySelector('audio');
                        if (audioPlayer) {
                            audioPlayer.src = data.audio_path;
                        } else {
                            const newAudioPlayer = document.createElement('audio');
                            newAudioPlayer.controls = true;
                            newAudioPlayer.src = data.audio_path;
                            row.querySelector('td:nth-child(5)').appendChild(newAudioPlayer);
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorMessage = document.getElementById('error-message');
                errorMessage.innerText = `Error updating card: ${error.message}`;
                errorMessage.classList.remove('hidden');
                setTimeout(() => {
                    errorMessage.classList.add('hidden');
                }, 5000);
            });
        });
    });

    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const row = e.target.closest('tr');
            row.querySelectorAll('.editable').forEach(item => {
                item.innerText = item.dataset.originalValue;
            });
            row.querySelector('.save-btn').classList.add('hidden');
            row.querySelector('.cancel-btn').classList.add('hidden');
        });
    });
</script>
@endpush
@endsection

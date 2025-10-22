@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-4xl font-bold text-center mb-8 heading-gradient">{{ $batch->name }}</h1>

    <div id="error-message" class="hidden alert-amber mb-6"></div>
    <div id="anki-status" class="hidden mb-6"></div>

    <div id="anki-connection-warning" class="alert-amber mb-6 p-4 rounded-lg border border-amber-300 flex items-start hidden">
        <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-amber-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div class="ml-3">
            <h4 class="font-bold text-amber-800">Connection to Anki Failed?</h4>
            <div class="text-sm text-amber-700 mt-1">
                <p>Please check the following:</p>
                <ul class="list-disc list-inside mt-2">
                    <li>Is Anki running with the AnkiConnect add-on installed? See the <a href="{{ route('tutorial') }}" class="underline font-medium">tutorial</a> for help.</li>
                    <li>Is your browser blocking the connection? Some browsers (like Brave) have shields or ad-blockers that can interfere. Try disabling them for this site.</li>
                </ul>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-green mb-6">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="alert-amber mb-6">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-amber mb-6">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-glass mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="md:col-span-2">
                <label for="deck" class="block text-sm font-medium text-slate-700 mb-2">Anki Deck</label>
                <select id="deck" name="deck" class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-3 focus:outline-none focus:ring-2 focus:ring-violet-500/40">
                    <option disabled selected>Loading decks...</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="button" id="add-to-anki-btn" class="btn-primary" disabled>
                    Add to Anki
                </button>
            </div>
        </div>
    </div>

    <div class="card-glass">
        <!-- Mobile view: Card list -->
        <div class="md:hidden">
            <div class="divide-y divide-slate-200">
                @foreach ($cards as $card)
                    <div class="p-4" data-card-id="{{ $card->id }}">
                        <div class="flex items-center mb-4">
                            <input type="checkbox" name="cards[]" value="{{ $card->id }}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 card-checkbox">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-slate-900 editable" data-field="front" contenteditable="true">{{ $card->front }}</div>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm text-slate-800">
                            <div class="editable" data-field="back" contenteditable="true">{!! nl2br(e($card->back)) !!}</div>
                            <div class="text-xs text-slate-500 editable" data-field="tts" contenteditable="true">{{ $card->tts }}</div>
                        </div>
                        <div class="mt-3 flex justify-between items-center">
                            @if ($card->audio_path)
                                <div class="custom-audio-player">
                                    <audio src="{{ Storage::url($card->audio_path) }}" preload="none"></audio>
                                    <button type="button" class="play-pause-btn btn-ghost">
                                        <svg class="play-icon h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                        <svg class="pause-icon hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
                                    </button>
                                </div>
                            @endif
                            <div class="space-x-2">
                                <button type="button" class="hidden save-btn btn-secondary">Save</button>
                                <button type="button" class="hidden cancel-btn btn-danger">Cancel</button>
                            </div>
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
                        <th scope="col" class="px-6 py-3">
                            <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-slate-500">Front</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-slate-500">Back</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-slate-500">TTS</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-slate-500">Audio</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($cards as $card)
                        <tr data-card-id="{{ $card->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="cards[]" value="{{ $card->id }}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 card-checkbox">
                            </td>
                            <td class="px-6 py-4"><span class="text-sm text-slate-900 editable" data-field="front" contenteditable="true" style="white-space: pre-wrap;">{{ $card->front }}</span></td>
                            <td class="px-6 py-4"><span class="text-sm text-slate-900 editable" data-field="back" contenteditable="true" style="white-space: pre-wrap;">{{ $card->back }}</span></td>
                            <td class="px-6 py-4"><span class="text-sm text-slate-900 editable" data-field="tts" contenteditable="true" style="white-space: pre-wrap;">{{ $card->tts }}</span></td>
                            <td class="px-6 py-4">
                                @if ($card->audio_path)
                                    <div class="custom-audio-player">
                                        <audio src="{{ Storage::url($card->audio_path) }}" preload="none"></audio>
                                        <button type="button" class="play-pause-btn btn-ghost">
                                            <svg class="play-icon h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                            <svg class="pause-icon hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button type="button" class="hidden save-btn btn-secondary">Save</button>
                                <button type="button" class="hidden cancel-btn btn-danger">Cancel</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-4 md:px-0">
            {{ $cards->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logic for both mobile and desktop
        function setupEditable(container) {
            container.querySelectorAll('.editable').forEach(item => {
                item.dataset.originalValue = item.innerText;
                item.addEventListener('input', function(e) {
                    const cardElement = e.target.closest('[data-card-id]');
                    cardElement.querySelector('.save-btn').classList.remove('hidden');
                    cardElement.querySelector('.cancel-btn').classList.remove('hidden');
                });
            });

            container.querySelectorAll('.save-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const cardElement = e.target.closest('[data-card-id]');
                    const cardId = cardElement.dataset.cardId;
                    const front = cardElement.querySelector('[data-field="front"]').innerText;
                    const back = cardElement.querySelector('[data-field="back"]').innerText;
                    const tts = cardElement.querySelector('[data-field="tts"]').innerText;

                    fetch(`/cards/${cardId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ front, back, tts })
                    })
                    .then(response => response.ok ? response.json() : response.json().then(e => Promise.reject(e)))
                    .then(data => {
                        if(data.success) {
                            cardElement.querySelector('.save-btn').classList.add('hidden');
                            cardElement.querySelector('.cancel-btn').classList.add('hidden');
                            cardElement.querySelectorAll('.editable').forEach(item => {
                                item.dataset.originalValue = item.innerText;
                            });
                            if (data.audio_path) {
                                const audioPlayer = cardElement.querySelector('audio');
                                if (audioPlayer) {
                                    audioPlayer.src = data.audio_path;
                                } else {
                                    // Simplified audio player recreation for now
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const errorMessage = document.getElementById('error-message');
                        errorMessage.innerText = `Error updating card: ${error.message}`;
                        errorMessage.classList.remove('hidden');
                        setTimeout(() => errorMessage.classList.add('hidden'), 5000);
                    });
                });
            });

            container.querySelectorAll('.cancel-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const cardElement = e.target.closest('[data-card-id]');
                    cardElement.querySelectorAll('.editable').forEach(item => {
                        item.innerText = item.dataset.originalValue;
                    });
                    cardElement.querySelector('.save-btn').classList.add('hidden');
                    cardElement.querySelector('.cancel-btn').classList.add('hidden');
                });
            });
        }

        setupEditable(document);

        document.querySelectorAll('.custom-audio-player').forEach(player => {
            const audio = player.querySelector('audio');
            const playPauseBtn = player.querySelector('.play-pause-btn');
            const playIcon = playPauseBtn.querySelector('.play-icon');
            const pauseIcon = playPauseBtn.querySelector('.pause-icon');

            playPauseBtn.addEventListener('click', () => {
                if (audio.paused) {
                    document.querySelectorAll('audio').forEach(a => a.pause());
                    audio.play();
                } else {
                    audio.pause();
                }
            });

            audio.addEventListener('play', () => {
                document.querySelectorAll('.custom-audio-player .pause-icon').forEach(i => i.classList.add('hidden'));
                document.querySelectorAll('.custom-audio-player .play-icon').forEach(i => i.classList.remove('hidden'));
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
            });

            audio.addEventListener('pause', () => {
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            });

            audio.addEventListener('ended', () => {
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            });
        });

        document.getElementById('select-all').addEventListener('click', function(event) {
            document.querySelectorAll('.card-checkbox').forEach(function(checkbox) {
                checkbox.checked = event.target.checked;
            });
        });

        // AnkiConnect script
        const ankiBtn = document.getElementById('add-to-anki-btn');
        const ankiStatusEl = document.getElementById('anki-status');
        const deckSelect = document.getElementById('deck');
        const ankiWarning = document.getElementById('anki-connection-warning');

        class AnkiConnect {
            constructor(urls) {
                this.urls = urls;
                this.url = null;
            }

            async findUrl() {
                if (this.url) return this.url;
                for (const url of this.urls) {
                    try {
                        await this.invoke('version', 6, {}, url);
                        this.url = url;
                        return url;
                    } catch (e) {
                        console.log(`AnkiConnect not found at ${url}`);
                    }
                }
                throw new Error('AnkiConnect not found on available ports.');
            }

            async invoke(action, version, params = {}, url = null) {
                const targetUrl = url || await this.findUrl();
                const response = await fetch(targetUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, version, params })
                });
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                return data.result;
            }

            async addNotesInChunks(notes, chunkSize) {
                let allResults = [];
                for (let i = 0; i < notes.length; i += chunkSize) {
                    const chunk = notes.slice(i, i + chunkSize);
                    showStatus(`Sending chunk ${i / chunkSize + 1}...`, 'blue');
                    const result = await this.invoke('addNotes', 6, { notes: chunk });
                    allResults = allResults.concat(result);
                }
                return allResults;
            }
        }

        async function loadAnkiDecks() {
            const ankiConnect = new AnkiConnect(['http://127.0.0.1:8765', 'http://localhost:8765']);
            try {
                const decks = await ankiConnect.invoke('deckNames', 6);
                ankiWarning.classList.add('hidden');
                deckSelect.innerHTML = ''; // Clear loading option
                decks.forEach(deck => {
                    const option = document.createElement('option');
                    option.value = deck;
                    option.textContent = deck;
                    deckSelect.appendChild(option);
                });
                ankiBtn.disabled = false;

                const savedDeck = localStorage.getItem('selectedDeck');
                if (savedDeck && decks.includes(savedDeck)) {
                    deckSelect.value = savedDeck;
                }
            } catch (error) {
                console.error('Could not fetch Anki decks:', error);
                deckSelect.innerHTML = '<option disabled selected>Could not connect to Anki</option>';
                ankiBtn.disabled = true;
                ankiWarning.classList.remove('hidden');
            }
        }

        deckSelect.addEventListener('change', function() {
            localStorage.setItem('selectedDeck', this.value);
        });

        async function addSelectedToAnki(cardIds, deck, batchId) {
            if (cardIds.length === 0) {
                showStatus('Please select at least one card.', 'amber');
                return;
            }

            ankiBtn.disabled = true;
            ankiBtn.innerText = 'Adding...';
            ankiWarning.classList.add('hidden');
            showStatus('Preparing notes...', 'blue');

            try {
                const response = await fetch('{{ route('anki.notes') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        cards: cardIds,
                        deck: deck,
                        batch_id: batchId
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch note data from the server.');
                }

                const { notes } = await response.json();
                showStatus(`Sending ${notes.length} notes to Anki...`, 'blue');

                const ankiConnect = new AnkiConnect(['http://127.0.0.1:8765', 'http://localhost:8765']);
                const results = await ankiConnect.addNotesInChunks(notes, 20);
                const totalCount = results.length;
                const addedCount = results.filter(r => r !== null).length;
                const duplicateCount = totalCount - addedCount;

                let message = `${addedCount}/${totalCount} cards added.`;
                if (duplicateCount > 0) {
                    message += ` ${duplicateCount} were duplicates.`;
                }
                showStatus(message, 'green');

            } catch (error) {
                console.error('AnkiConnect Error:', error);
                let errorMessage = 'An error occurred.';
                if (error.message.includes('Failed to fetch') || error.message.includes('AnkiConnect not found')) {
                    errorMessage = 'Could not connect to AnkiConnect.';
                    ankiWarning.classList.remove('hidden');
                } else {
                    errorMessage = error.message;
                }
                showStatus(errorMessage, 'amber');
            } finally {
                ankiBtn.disabled = false;
                ankiBtn.innerText = 'Add to Anki';
            }
        }

        function showStatus(message, type) {
            ankiStatusEl.innerHTML = `<p>${message}</p>`;
            ankiStatusEl.className = `alert-${type} mb-6`;
            ankiStatusEl.classList.remove('hidden');
        }

        ankiBtn.addEventListener('click', () => {
            const selectedCardIds = Array.from(document.querySelectorAll('.card-checkbox:checked')).map(cb => cb.value);
            const deck = document.getElementById('deck').value;
            const batchId = '{{ $batch->id }}';
            addSelectedToAnki(selectedCardIds, deck, batchId);
        });

        // Initial load
        loadAnkiDecks();
    });
</script>
@endpush

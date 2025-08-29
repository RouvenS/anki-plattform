@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-4xl font-bold text-center mb-8 heading-gradient">{{ $batch->name }}</h1>

    <div id="error-message" class="hidden alert-amber mb-6"></div>
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

    <div class="card-glass mb-8">
        <form action="{{ route('cards.add-to-anki') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <input type="hidden" name="batch_id" value="{{ $batch->id }}">
            <div class="md:col-span-2">
                <label for="deck" class="block text-sm font-medium text-slate-700 mb-2">Anki Deck</label>
                <select id="deck" name="deck" class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-3 focus:outline-none focus:ring-2 focus:ring-violet-500/40">
                    @if(!empty($decks))
                        @foreach ($decks as $deck)
                            <option>{{ $deck }}</option>
                        @endforeach
                    @else
                        <option disabled>Could not connect to Anki. Is it running?</option>
                    @endif
                </select>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="btn-primary" @if(empty($decks)) disabled @endif>
                    Add to Anki
                </button>
            </div>
        </form>
    </div>

    <div class="card-glass">
        <div class="overflow-x-auto">
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
                            <td class="px-6 py-4"><span class="text-sm text-slate-900 editable" data-field="front" contenteditable="true">{{ $card->front }}</span></td>
                            <td class="px-6 py-4"><span class="text-sm text-slate-900 editable" data-field="back" contenteditable="true">{{ $card->back }}</span></td>
                            <td class="px-6 py-4"><span class="text-sm text-slate-900 editable" data-field="tts" contenteditable="true">{{ $card->tts }}</span></td>
                            <td class="px-6 py-4">
                                @if ($card->audio_path)
                                    <div class="custom-audio-player">
                                        <audio src="{{ Storage::url($card->audio_path) }}" preload="none"></audio>
                                        <button class="play-pause-btn btn-ghost">
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
        <div class="mt-4">
            {{ $cards->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.custom-audio-player').forEach(player => {
        const audio = player.querySelector('audio');
        const playPauseBtn = player.querySelector('.play-pause-btn');
        const playIcon = playPauseBtn.querySelector('.play-icon');
        const pauseIcon = playPauseBtn.querySelector('.pause-icon');
        const progressBar = player.querySelector('.progress-bar');

        playPauseBtn.addEventListener('click', () => {
            if (audio.paused) {
                document.querySelectorAll('audio').forEach(otherAudio => {
                    if (otherAudio !== audio) {
                        otherAudio.pause();
                    }
                });
                audio.play();
            } else {
                audio.pause();
            }
        });

        audio.addEventListener('play', () => {
            document.querySelectorAll('.custom-audio-player').forEach(p => {
                p.querySelector('.play-icon').classList.remove('hidden');
                p.querySelector('.pause-icon').classList.add('hidden');
            });
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
            progressBar.style.width = '0%';
        });

        audio.addEventListener('timeupdate', () => {
            const progress = (audio.currentTime / audio.duration) * 100;
            progressBar.style.width = `${progress}%`;
        });
    });

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

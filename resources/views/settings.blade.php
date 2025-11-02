@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    <h1 class="text-4xl font-bold text-center heading-gradient">Settings</h1>

    @if (session('success'))
        <div class="alert-green">
            <div>
                <p class="font-medium">Success!</p>
                <p class="text-xs mt-1">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Profile Settings -->
    <div class="relative group">
        <div class="card-glass">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Profile</h2>
            <form method="POST" action="{{ route('settings.profile.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Name</label>
                    <input id="name" name="name" type="text"
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40 @error('name') border-red-500 @enderror"
                           value="{{ old('name', $user->name) }}">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                    <input id="email" name="email" type="email"
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40 @error('email') border-red-500 @enderror"
                           value="{{ old('email', $user->email) }}">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- API Keys -->
    <div class="relative group">
        <div class="card-glass">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">API Keys</h2>

            <form method="POST" action="{{ route('settings.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="openai_api_key" class="block text-sm font-medium text-slate-700 mb-2">OpenAI API Key</label>
                    <input id="openai_api_key" name="openai_api_key" type="password"
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40"
                           placeholder="sk-..."
                           value="{{ $user->openai_api_key }}">
                </div>

                <div>
                    <label for="anki_api_key" class="block text-sm font-medium text-slate-700 mb-2">Anki API Key</label>
                    <input id="anki_api_key" name="anki_api_key" type="text"
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40"
                           placeholder="Enter your Anki Connect API Key"
                           value="{{ $user->anki_api_key }}">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Save API Keys</button>
                </div>
            </form>

            <hr class="my-8 border-slate-200">

            <div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Anki Connect Configuration</h3>
                <p class="text-sm text-slate-600 mb-4">Copy this configuration into your Anki Connect addon's config file to connect your Anki desktop app.</p>
                <div class="bg-slate-800 text-slate-100 rounded-xl p-4 font-mono text-sm relative">
                    <button class="absolute top-2 right-2 bg-slate-600 hover:bg-slate-500 text-white text-xs font-bold py-1 px-2 rounded-md" onclick="copyToClipboard(this)">Copy</button>
                    <pre><code id="anki-config">{{ $ankiConnectConfigJson }}</code></pre>
                </div>
                 @error('anki_api_key')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Security -->
    <div class="relative group">
        <div class="card-glass">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Security</h2>
            <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')
                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700 mb-2">Current Password</label>
                    <input id="current_password" name="current_password" type="password"
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40 @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                    <input id="new_password" name="new_password" type="password"
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40 @error('new_password') border-red-500 @enderror">
                     @error('new_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirm New Password</label>
                    <input id="new_password_confirmation" name="new_password_confirmation" type="password"
                           class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500/40">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danger Zone -->
    <div x-data="{ open: false }" class="relative group">
        <div class="card-glass border-red-500/20">
            <h2 class="text-2xl font-bold text-red-800 mb-4">Danger Zone</h2>
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-800">Delete Account</h3>
                    <p class="text-sm text-slate-600">Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>
                </div>
                <button @click="open = true" class="btn-danger">Delete Account</button>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="open" x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @click.away="open = false">
            <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full">
                <h2 class="text-2xl font-bold text-slate-800 mb-4">Are you sure?</h2>
                <p class="text-slate-600 mb-6">This action cannot be undone. This will permanently delete your account. Please type your password to confirm.</p>
                <form method="POST" action="{{ route('settings.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <div class="mb-4">
                         <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                         <input id="password" name="password" type="password"
                                class="w-full rounded-xl border border-slate-200 bg-white/70 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500/40 @error('password') border-red-500 @enderror"
                                required>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="open = false" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-danger">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(button) {
        const codeElement = document.getElementById('anki-config');
        navigator.clipboard.writeText(codeElement.innerText).then(() => {
            button.innerText = 'Copied!';
            setTimeout(() => {
                button.innerText = 'Copy';
            }, 2000);
        });
    }
</script>
@endsection
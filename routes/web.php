<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\PromptController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', HomeController::class)->name('home');

Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest');

Route::post('/login', function(Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::get('/register', [RegisterController::class, 'create'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/tutorial', function () {
    return view('tutorial');
})->name('tutorial');

Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::delete('/settings', [SettingsController::class, 'destroy'])->name('settings.destroy');

    Route::post('/cards', [CardController::class, 'store'])->name('cards.store');
    Route::resource('batches', BatchController::class)->except(['index']);
    Route::resource('cards', CardController::class);
    Route::resource('prompts', PromptController::class);
    Route::post('prompts/{prompt}/duplicate', [PromptController::class, 'duplicate'])->name('prompts.duplicate');
    Route::post('/anki/notes', [CardController::class, 'buildAnkiNotes'])->name('anki.notes');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\BatchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [HomeController::class, 'loginForm'])->name('login');
Route::post('/login', [HomeController::class, 'login']);

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

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

Route::get('/settings', [SettingsController::class, 'index'])->name('settings')->middleware('auth');
Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store')->middleware('auth');

Route::post('/cards', [CardController::class, 'store'])->name('cards.store')->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::resource('batches', BatchController::class);
    Route::resource('cards', CardController::class);
    Route::resource('prompts', \App\Http\Controllers\PromptController::class);
    Route::post('prompts/{prompt}/duplicate', [\App\Http\Controllers\PromptController::class, 'duplicate'])->name('prompts.duplicate');
    Route::post('/cards/add-to-anki', [CardController::class, 'addToAnki'])->name('cards.add-to-anki');
});

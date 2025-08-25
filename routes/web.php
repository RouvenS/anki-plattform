<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', [HomeController::class, 'index']);

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
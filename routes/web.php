<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware(['inactivity', 'guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware(['auth', 'inactivity'])->group(function () {
    Route::get('/dashboard', function () {
        return view('layout.home');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/daftar-laporan', function () {
        return view('daftarlaporan.daftarpengguna');
    })->name('daftar-laporan');
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/auto-logout', function() {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login')
        ->with('message', 'Anda telah logout karena tidak aktif selama 30 menit');
})->name('auto-logout');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;

// Redirect root ke login jika belum login, ke welcome jika sudah
Route::get('/', function () {
    return redirect()->route('welcome');
});

// Route Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman Welcome (Hanya untuk user yang login)
Route::middleware(['auth'])->group(function () {
    Route::get('/welcome', [WelcomeController::class, 'showWelcome'])->name('welcome');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Untuk Login dan Logout
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Proteksi Dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return "Berhasil Login!";
    });
});

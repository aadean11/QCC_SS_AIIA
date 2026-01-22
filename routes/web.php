<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AdminQccController;
use App\Http\Controllers\QccStepController;

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

// Admin QCC
Route::middleware(['auth'])->group(function () {
    // Route Dashboard Admin QCC
    Route::get('/qcc/admin/dashboard', [AdminQccController::class, 'index'])->name('qcc.admin.dashboard');

    // Route Master Steps QCC
    Route::get('/qcc/admin/master-steps', [AdminQccController::class, 'masterSteps'])->name('qcc.admin.master_steps');
    Route::post('/qcc/admin/master-steps', [AdminQccController::class, 'storeStep'])->name('qcc.admin.store_step');
    Route::put('/qcc/admin/master-steps/{id}', [AdminQccController::class, 'updateStep'])->name('qcc.admin.update_step');
    Route::delete('/qcc/admin/master-steps/{id}', [AdminQccController::class, 'deleteStep'])->name('qcc.admin.delete_step');

    // Route Master Periods
    Route::get('/qcc/admin/master-periods', [AdminQccController::class, 'masterPeriods'])->name('qcc.admin.master_periods');
    Route::post('/qcc/admin/master-periods', [AdminQccController::class, 'storePeriod'])->name('qcc.admin.store_period');
    Route::put('/qcc/admin/master-periods/{id}', [AdminQccController::class, 'updatePeriod'])->name('qcc.admin.update_period');
    Route::delete('/qcc/admin/master-periods/{id}', [AdminQccController::class, 'deletePeriod'])->name('qcc.admin.delete_period');
});
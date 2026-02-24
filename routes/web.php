<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AdminQccController;
use App\Http\Controllers\QccStepController;
use App\Http\Controllers\KaryawanQccController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\QccApprovalController;

// Redirect root ke login jika belum login, ke welcome jika sudah
Route::get('/', function () {
    return redirect()->route('welcome');
});

// Route Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/check-role', [AuthController::class, 'checkRole'])->name('check.role');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman Welcome (Hanya untuk user yang login)
Route::middleware(['auth'])->group(function () {
    Route::get('/welcome', [WelcomeController::class, 'showWelcome'])->name('welcome');

    // Master Employee (Karyawan)
    Route::get('/admin/master-employee', [EmployeeController::class, 'index'])->name('admin.master_employee.index');
    Route::post('/admin/master-employee', [EmployeeController::class, 'store'])->name('admin.master_employee.store');
    Route::put('/admin/master-employee/{id}', [EmployeeController::class, 'update'])->name('admin.master_employee.update');
    Route::delete('/admin/master-employee/{id}', [EmployeeController::class, 'destroy'])->name('admin.master_employee.destroy');
});

// Monitoring QCC dan SS (Admin & Karyawan)
Route::middleware(['auth'])->group(function () {

    // Dashboard Admin QCC
    Route::get('/qcc/admin/dashboard', [AdminQccController::class, 'index'])->name('qcc.admin.dashboard');

    // Master Steps QCC
    Route::get('/qcc/admin/master-steps', [AdminQccController::class, 'masterSteps'])->name('qcc.admin.master_steps');
    Route::post('/qcc/admin/master-steps', [AdminQccController::class, 'storeStep'])->name('qcc.admin.store_step');
    Route::put('/qcc/admin/master-steps/{id}', [AdminQccController::class, 'updateStep'])->name('qcc.admin.update_step');
    Route::delete('/qcc/admin/master-steps/{id}', [AdminQccController::class, 'deleteStep'])->name('qcc.admin.delete_step');

    // Master Periods
    Route::get('/qcc/admin/master-periods', [AdminQccController::class, 'masterPeriods'])->name('qcc.admin.master_periods');
    Route::post('/qcc/admin/master-periods', [AdminQccController::class, 'storePeriod'])->name('qcc.admin.store_period');
    Route::put('/qcc/admin/master-periods/{id}', [AdminQccController::class, 'updatePeriod'])->name('qcc.admin.update_period');
    Route::delete('/qcc/admin/master-periods/{id}', [AdminQccController::class, 'deletePeriod'])->name('qcc.admin.delete_period');

    // Master Target QCC
    Route::get('/qcc/admin/master-targets', [AdminQccController::class, 'masterTargets'])->name('qcc.admin.master_targets');
    Route::post('/qcc/admin/master-targets', [AdminQccController::class, 'storeTarget'])->name('qcc.admin.store_target');
    Route::put('/qcc/admin/master-targets/{id}', [AdminQccController::class, 'updateTarget'])->name('qcc.admin.update_target');
    Route::delete('/qcc/admin/master-targets/{id}', [AdminQccController::class, 'deleteTarget'])->name('qcc.admin.delete_target');

    // Route untuk melihat seluruh progres circle (Admin)
    Route::get('/qcc/admin/all-progress', [AdminQccController::class, 'allCircleProgress'])->name('qcc.admin.all_progress');

    // Master Circles & Members (Data Kelompok QCC)
    Route::get('/qcc/admin/master-circles', [AdminQccController::class, 'masterCircles'])->name('qcc.admin.master_circles');
    Route::post('/qcc/admin/master-circles', [AdminQccController::class, 'storeCircle'])->name('qcc.admin.store_circle');

    // Monitoring Progress (Monitoring File Transaksi Circle)
    Route::get('/qcc/admin/monitoring-progress/{circle_id}', [AdminQccController::class, 'monitoringProgress'])->name('qcc.admin.monitoring_progress');

    // Dashboard Karyawan QCC
    Route::get('/qcc/karyawan/dashboard', [KaryawanQccController::class, 'dashboard'])->name('qcc.karyawan.dashboard');

    // Route Karyawan QCC
    Route::get('/qcc/karyawan/my-circle', [KaryawanQccController::class, 'myCircle'])->name('qcc.karyawan.my_circle');   
    Route::post('/qcc/karyawan/store-circle', [KaryawanQccController::class, 'storeCircle'])->name('qcc.karyawan.store_circle');
    Route::put('/qcc/karyawan/update-circle/{id}', [KaryawanQccController::class, 'updateCircle'])->name('qcc.karyawan.update_circle');
    Route::delete('/qcc/karyawan/delete-circle/{id}', [KaryawanQccController::class, 'deleteCircle'])->name('qcc.karyawan.delete_circle');
    Route::get('/qcc/karyawan/roadmap', [KaryawanQccController::class, 'roadmap'])->name('qcc.karyawan.roadmap');

    // Route Tema (Sub-menu baru)
    Route::get('/qcc/karyawan/themes', [KaryawanQccController::class, 'themes'])->name('qcc.karyawan.themes');
    Route::post('/qcc/karyawan/store-theme', [KaryawanQccController::class, 'storeTheme'])->name('qcc.karyawan.store_theme');
    Route::put('/qcc/karyawan/update-theme/{id}', [KaryawanQccController::class, 'updateTheme'])->name('qcc.karyawan.update_theme');
    Route::delete('/qcc/karyawan/delete-theme/{id}', [KaryawanQccController::class, 'deleteTheme'])->name('qcc.karyawan.delete_theme');
    Route::get('/qcc/karyawan/progress', [KaryawanQccController::class, 'progress'])->name('qcc.karyawan.progress');
    Route::post('/qcc/karyawan/upload-file', [KaryawanQccController::class, 'uploadFile'])->name('qcc.karyawan.upload_file');

    // Approval Registrasi Circle Baru
    Route::get('/qcc/approval/circle', [QccApprovalController::class, 'indexCircle'])->name('qcc.approval.circle');
    Route::post('/qcc/approval/circle/process/{id}', [QccApprovalController::class, 'processCircle'])->name('qcc.approval.process_circle');

    // Approval Progres PDCA (Step 1-8)
    Route::get('/qcc/approval/progress', [QccApprovalController::class, 'index'])->name('qcc.approval.progress');
    Route::post('/qcc/approval/progress/process/{id}', [QccApprovalController::class, 'process'])->name('qcc.approval.process');
});


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AdminQccController;
use App\Http\Controllers\QccStepController;
use App\Http\Controllers\KaryawanQccController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\QccApprovalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MasterOrgController;
use App\Http\Controllers\AdminSsController;
use App\Http\Controllers\SsApprovalController;
use App\Http\Controllers\KaryawanSsController;

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
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Halaman Welcome (Hanya untuk user yang login)
Route::middleware(['auth'])->group(function () {
    Route::get('/welcome', [WelcomeController::class, 'showWelcome'])->name('welcome');

    // Master Employee (Karyawan)
    Route::get('/admin/master-employee', [EmployeeController::class, 'index'])->name('admin.master_employee.index');
    Route::post('/admin/master-employee', [EmployeeController::class, 'store'])->name('admin.master_employee.store');
    Route::put('/admin/master-employee/{id}', [EmployeeController::class, 'update'])->name('admin.master_employee.update');
    Route::delete('/admin/master-employee/{id}', [EmployeeController::class, 'destroy'])->name('admin.master_employee.destroy');

    // Master User (Admin only)
    Route::get('/admin/master-user', [UserController::class, 'index'])->name('admin.master_user.index');
    Route::post('/admin/master-user', [UserController::class, 'store'])->name('admin.master_user.store');
    Route::put('/admin/master-user/{id}', [UserController::class, 'update'])->name('admin.master_user.update');
    Route::delete('/admin/master-user/{id}', [UserController::class, 'destroy'])->name('admin.master_user.destroy');

    // Master Organisasi (Admin only)
    Route::get('/admin/master-departments', [MasterOrgController::class, 'departmentsIndex'])->name('admin.master_departments.index');
    Route::post('/admin/master-departments', [MasterOrgController::class, 'storeDepartment'])->name('admin.master_departments.store');
    Route::put('/admin/master-departments/{id}', [MasterOrgController::class, 'updateDepartment'])->name('admin.master_departments.update');
    Route::delete('/admin/master-departments/{id}', [MasterOrgController::class, 'destroyDepartment'])->name('admin.master_departments.destroy');

    Route::get('/admin/master-sections', [MasterOrgController::class, 'sectionsIndex'])->name('admin.master_sections.index');
    Route::post('/admin/master-sections', [MasterOrgController::class, 'storeSection'])->name('admin.master_sections.store');
    Route::put('/admin/master-sections/{id}', [MasterOrgController::class, 'updateSection'])->name('admin.master_sections.update');
    Route::delete('/admin/master-sections/{id}', [MasterOrgController::class, 'destroySection'])->name('admin.master_sections.destroy');

    Route::get('/admin/master-sub-sections', [MasterOrgController::class, 'subSectionsIndex'])->name('admin.master_sub_sections.index');
    Route::post('/admin/master-sub-sections', [MasterOrgController::class, 'storeSubSection'])->name('admin.master_sub_sections.store');
    Route::put('/admin/master-sub-sections/{id}', [MasterOrgController::class, 'updateSubSection'])->name('admin.master_sub_sections.update');
    Route::delete('/admin/master-sub-sections/{id}', [MasterOrgController::class, 'destroySubSection'])->name('admin.master_sub_sections.destroy');
});

// Monitoring QCC dan SS (Admin & Karyawan)
Route::middleware(['auth'])->group(function () {

    // Dashboard Admin QCC
    Route::get('/qcc/admin/dashboard', [AdminQccController::class, 'index'])->name('qcc.admin.dashboard');

    // Master Schedule QCC
    Route::get('/qcc/admin/master-schedule', [AdminQccController::class, 'masterSchedule'])->name('qcc.admin.master_schedule');

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

    // Master Seven Tools QCC
    Route::get('/qcc/admin/master-seven-tools', [AdminQccController::class, 'masterSevenTools'])->name('qcc.admin.master_seven_tools');
    Route::post('/qcc/admin/master-seven-tools', [AdminQccController::class, 'storeSevenTool'])->name('qcc.admin.store_seven_tool');
    Route::put('/qcc/admin/master-seven-tools/{id}', [AdminQccController::class, 'updateSevenTool'])->name('qcc.admin.update_seven_tool');
    Route::delete('/qcc/admin/master-seven-tools/{id}', [AdminQccController::class, 'deleteSevenTool'])->name('qcc.admin.delete_seven_tool');

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

    // Approval SS (SPV / KDP — role employee)
    Route::get('/ss/approval/spv', [SsApprovalController::class, 'indexSpv'])->name('ss.approval.spv');
    Route::get('/ss/approval/spv/{id}/review', [SsApprovalController::class, 'reviewSpvForm'])->name('ss.approval.spv.review');
    Route::post('/ss/approval/spv/{id}/review', [SsApprovalController::class, 'reviewSpvStore'])->name('ss.approval.spv.store');
    Route::get('/ss/approval/kdp', [SsApprovalController::class, 'indexKdp'])->name('ss.approval.kdp');
    Route::get('/ss/approval/kdp/{id}/review', [SsApprovalController::class, 'reviewKdpForm'])->name('ss.approval.kdp.review');
    Route::post('/ss/approval/kdp/{id}/review', [SsApprovalController::class, 'reviewKdpStore'])->name('ss.approval.kdp.store');
});

// Group untuk karyawan SS
Route::prefix('ss/karyawan')->name('ss.karyawan.')->middleware('auth')->group(function () {
    Route::get('/', [KaryawanSsController::class, 'index'])->name('index');
    Route::get('/create', [KaryawanSsController::class, 'create'])->name('create');
    Route::post('/store', [KaryawanSsController::class, 'store'])->name('store');
    Route::get('/{id}', [KaryawanSsController::class, 'show'])->name('show');
});

// Group untuk admin SS
Route::prefix('ss/admin')->name('ss.admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminSsController::class, 'dashboard'])->name('dashboard');
    Route::get('/master-targets', [AdminSsController::class, 'masterTargets'])->name('master_targets');
    Route::post('/master-targets', [AdminSsController::class, 'storeTarget'])->name('store_target');
    Route::put('/master-targets/{id}', [AdminSsController::class, 'updateTarget'])->name('update_target');
    Route::delete('/master-targets/{id}', [AdminSsController::class, 'deleteTarget'])->name('delete_target');
    Route::get('/master-scoring', [AdminSsController::class, 'masterScoring'])->name('master_scoring');
    Route::post('/master-scoring', [AdminSsController::class, 'storeScoring'])->name('store_scoring');
    Route::put('/master-scoring/{id}', [AdminSsController::class, 'updateScoring'])->name('update_scoring');
    Route::delete('/master-scoring/{id}', [AdminSsController::class, 'deleteScoring'])->name('delete_scoring');
    Route::get('/review-komite', [AdminSsController::class, 'adminReviewIndex'])->name('review.index');
    Route::get('/review-komite/{id}', [AdminSsController::class, 'adminReviewForm'])->name('review.form');
    Route::post('/review-komite/{id}', [AdminSsController::class, 'adminReviewStore'])->name('review.store');
    Route::get('/submissions', [AdminSsController::class, 'submissions'])->name('submissions');
    Route::get('/submissions/export-pdf', [AdminSsController::class, 'exportSubmissionsPdf'])->name('submissions.export_pdf');
    Route::get('/submissions/{id}', [AdminSsController::class, 'show'])->name('show');
    Route::get('/reward/{id}', [AdminSsController::class, 'rewardForm'])->name('reward.form');
    Route::post('/reward/{id}', [AdminSsController::class, 'rewardStore'])->name('reward.store');
});

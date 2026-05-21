<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\User;
use App\Models\QccCircle;
use App\Models\QccCircleStepTransaction;
use App\Models\SsSubmission;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gunakan Paginator Tailwind agar paging horizontal lancar
        \Illuminate\Pagination\Paginator::useTailwind();

        // Bagikan data notifikasi secara otomatis ke layout 'welcome'
        View::composer('welcome', function ($view) {
            $view->with([
                'countCircle' => 0,
                'countProgress' => 0,
                'countQccApproval' => 0,
                'countSsSpvApproval' => 0,
                'countSsKdpApproval' => 0,
                'countSsApproval' => 0,
            ]);

            if (Auth::check()) {
                $authUser = Auth::user();
                $employee = $authUser?->employee ?: Employee::with('subSection.section')->where('npk', $authUser?->npk)->first();

                if ($employee && in_array($employee->occupation, ['SPV', 'KDP'])) {
                    $myDept = $employee->getDeptCode();
                    
                    // Penentuan Status WAITING
                    $stCircle = ($employee->occupation === 'KDP') ? 'WAITING KDP' : 'WAITING SPV';
                    $stProgress = ($employee->occupation === 'KDP') ? 'WAITING KDP' : 'WAITING SPV';

                    // HITUNG HANYA DEPARTEMEN SENDIRI
                    $countCircle = QccCircle::where('department_code', $myDept)->where('status', $stCircle)->count();
                    $countProgress = QccCircleStepTransaction::whereHas('circle', fn($q) => $q->where('department_code', $myDept))
                                    ->where('status', $stProgress)->count();

                    $view->with([
                        'countCircle' => $countCircle,
                        'countProgress' => $countProgress,
                        'countQccApproval' => $countCircle + $countProgress,
                    ]);
                }

                if (session('active_role') === 'admin') {
                    $countSsSpvApproval = SsSubmission::where('status', 'assessed')->count();
                    $countSsKdpApproval = SsSubmission::where('status', 'kdp_review')->count();

                    $view->with([
                        'countSsSpvApproval' => $countSsSpvApproval,
                        'countSsKdpApproval' => $countSsKdpApproval,
                        'countSsApproval' => $countSsSpvApproval + $countSsKdpApproval,
                    ]);
                }
            }
        });
    }
}

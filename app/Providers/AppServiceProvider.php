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
            if (Auth::check()) {
                $npk = session('auth_npk');
                $user = Employee::with('subSection.section')->where('npk', $npk)->first() ?? User::where('npk', $npk)->first();

                if ($user && ($user->occupation === 'SPV' || $user->occupation === 'KDP')) {
                    $myDept = $user->getDeptCode();
                    
                    // Penentuan Status WAITING
                    $stCircle = ($user->occupation === 'KDP') ? 'WAITING KDP' : 'WAITING SPV';
                    $stProgress = ($user->occupation === 'KDP') ? 'WAITING KADEPT' : 'WAITING SPV';

                    // HITUNG HANYA DEPARTEMEN SENDIRI
                    $countCircle = QccCircle::where('department_code', $myDept)->where('status', $stCircle)->count();
                    $countProgress = QccCircleStepTransaction::whereHas('circle', fn($q) => $q->where('department_code', $myDept))
                                    ->where('status', $stProgress)->count();

                    $view->with(['countCircle' => $countCircle, 'countProgress' => $countProgress]);
                }
            }
        });
    }
}

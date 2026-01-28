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
                
                // Ambil data user lengkap agar tahu jabatannya (occupation)
                $user = Employee::where('npk', $npk)->first() ?? User::where('npk', $npk)->first();

                if ($user) {
                    // Logic penentuan status yang harus di-approve berdasarkan jabatan
                    if ($user->occupation === 'KDP') {
                        $statusCircle = 'WAITING KDP';
                        $statusProgress = 'WAITING KADEPT';
                    } else {
                        $statusCircle = 'WAITING SPV';
                        $statusProgress = 'WAITING SPV';
                    }

                    $countCircle = QccCircle::where('status', $statusCircle)->count();
                    $countProgress = QccCircleStepTransaction::where('status', $statusProgress)->count();

                    $view->with([
                        'countCircle' => $countCircle,
                        'countProgress' => $countProgress
                    ]);
                }
            } else {
                // Jika belum login, set default 0 agar tidak error
                $view->with(['countCircle' => 0, 'countProgress' => 0]);
            }
        });
    }
}

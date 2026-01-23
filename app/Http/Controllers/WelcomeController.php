<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\QccCircle;

class WelcomeController extends Controller
{
    public function showWelcome()
    {
        $npk = session('auth_npk');
        $loginAs = session('login_as');

        if (!$npk) {
            Auth::logout();
            return redirect('/login');
        }

        // Cari data user dari tabel manapun berdasarkan NPK yang login
        // Kita prioritaskan m_employees agar dapet data jabatan (job), jika tidak ada cari di users
        $user = Employee::with('job')->where('npk', $npk)->first() 
                ?? User::where('npk', $npk)->first();

        $jumlahQcc = \App\Models\QccCircle::count();
        $jumlahSs = 100;

        return view('home', compact('user', 'jumlahQcc', 'jumlahSs'));
    }
}
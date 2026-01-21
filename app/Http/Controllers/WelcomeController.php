<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;

class WelcomeController extends Controller
{
    public function showWelcome()
    {
        // Mengambil data user yang sedang login beserta relasi jabatan
        $user = Employee::with('job')->find(Auth::id());

        $jumlahQcc = 100; 
        $jumlahSs = 100;

        return view('welcome', compact('user', 'jumlahQcc', 'jumlahSs'));
    }
}
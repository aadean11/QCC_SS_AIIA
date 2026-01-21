<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\QccCircle; // Import model QCC

class AdminQccController extends Controller
{
    public function index()
    {
        $user = Employee::with('job')->find(Auth::id());
        
        // Tambahkan ini agar layout 'welcome' tidak error
        $jumlahQcc = QccCircle::count(); 
        $jumlahSs = 100; // Sesuaikan dengan logika Anda

        $stats = [
            'total_circles' => $jumlahQcc,
            'active_periods' => 2,
            'need_review' => 15,
            'completed' => 45
        ];

        $circles = []; 

        // Kirimkan jumlahQcc dan jumlahSs ke view
        return view('qcc.admin.dashboard', compact('user', 'stats', 'circles', 'jumlahQcc', 'jumlahSs'));
    }
}   
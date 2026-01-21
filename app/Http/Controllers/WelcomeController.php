<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\QccCircle; // Import model QCC
// use App\Models\SuggestionSystem; // Import model SS jika sudah ada

class WelcomeController extends Controller
{
    public function showWelcome()
    {
        $user = Employee::with('job')->find(Auth::id());
        $jumlahQcc = QccCircle::count(); 
        $jumlahSs = 100;

        return view('home', compact('user', 'jumlahQcc', 'jumlahSs'));
    }
}
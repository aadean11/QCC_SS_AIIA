<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\QccCircle;
use App\Models\Department;
use App\Models\User;

class WelcomeController extends Controller
{
    public function showWelcome()
    {
        $npk = session('auth_npk');
        $activeRole = session('active_role'); // 'admin' atau 'employee'

        if (!$npk) {
            Auth::logout();
            return redirect('/login');
        }

        // 1. Ambil data user dengan relasi lengkap
        $user = Employee::with(['subSection.section.department.division', 'job'])
                ->where('npk', $npk)
                ->first() 
                ?? User::where('npk', $npk)->first();

        // 2. Inisialisasi Query
        $qccQuery = QccCircle::query();
        $viewScope = "All Company"; // Default Label

        // 3. Logika Filter berdasarkan Otoritas (Jika bukan role Admin)
        if ($activeRole !== 'admin') {
            $deptCode = $user->getDeptCode();
            
            if ($user->occupation === 'GMR') {
                // GMR: Lihat semua departemen dalam divisinya
                $myDept = Department::where('code', $deptCode)->first();
                $divCode = $myDept ? $myDept->code_division : null;
                $divName = $myDept && $myDept->division ? $myDept->division->name : 'N/A';

                $qccQuery->whereHas('department', function($q) use ($divCode) {
                    $q->where('code_division', $divCode);
                });
                $viewScope = "Division: " . $divName;
            } 
            elseif (in_array($user->occupation, ['KDP', 'SPV'])) {
                // KDP & SPV: Hanya departemen sendiri
                $qccQuery->where('department_code', $deptCode);
                $deptName = $user->getDepartment()->name ?? $deptCode;
                $viewScope = "Department: " . $deptName;
            } 
            else {
                // Employee Biasa: Hanya Circle yang dia ikuti
                $qccQuery->whereHas('members', function($q) use ($npk) {
                    $q->where('employee_npk', $npk);
                });
                $viewScope = "Personal & My Circle";
            }
        }

        $jumlahQcc = $qccQuery->count();
        $jumlahSs = 0; // Sesuaikan dengan logika filter SS Anda nanti jika sudah ada tabelnya

        return view('home', compact('user', 'jumlahQcc', 'jumlahSs', 'viewScope'));
    }
}
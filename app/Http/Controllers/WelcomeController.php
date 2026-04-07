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
        // Ambil user yang sedang login dari tabel users
        $userAuth = Auth::user();
        
        if (!$userAuth) {
            Auth::logout();
            return redirect('/login');
        }

        $activeRole = session('active_role'); // 'admin' atau 'employee'

        // Ambil data employee dari relasi user (jika ada)
        $employee = $userAuth->employee;
        
        // Untuk kompatibilitas view, kita tetap pakai variabel $user
        // Jika employee tidak ditemukan, fallback ke data dari User (tabel users)
        if ($employee) {
            $user = $employee;
        } else {
            // Jika tidak ada data employee, buat objek Employee kosong? 
            // Atau bisa juga menggunakan User sebagai fallback, 
            // tapi method getDeptCode() dll tidak tersedia.
            // Solusi: redirect dengan error atau set user = userAuth (tabel users)
            // Karena view mungkin membutuhkan properti seperti nama, npk, dan method getDeptCode()
            // Lebih aman jika employee wajib ada. Jika tidak, arahkan ke halaman error.
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan. Hubungi administrator.');
        }

        // Inisialisasi Query
        $qccQuery = QccCircle::query();
        $viewScope = "All Company"; // Default Label

        // Logika Filter berdasarkan Otoritas (Jika bukan role Admin)
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
                $qccQuery->whereHas('members', function($q) use ($user) {
                    $q->where('employee_npk', $user->npk);
                });
                $viewScope = "Personal & My Circle";
            }
        }

        $jumlahQcc = $qccQuery->count();
        $jumlahSs = 0; // Sesuaikan dengan logika filter SS Anda nanti jika sudah ada tabelnya

        return view('home', compact('user', 'jumlahQcc', 'jumlahSs', 'viewScope'));
    }
}
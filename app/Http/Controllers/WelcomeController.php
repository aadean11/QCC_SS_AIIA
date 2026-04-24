<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\QccCircle;
use App\Models\Department;
use App\Models\User;
use App\Models\SsSubmission; // Import model SS

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
        
        if ($employee) {
            $user = $employee;
        } else {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan. Hubungi administrator.');
        }

        // ========== FILTER QCC ==========
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

        // ========== FILTER SS (Suggestion System) ==========
        $ssQuery = SsSubmission::query();

        if ($activeRole !== 'admin') {
            $deptCode = $user->getDeptCode();

            if ($user->occupation === 'GMR') {
                // GMR: Lihat semua SS dalam divisinya
                $myDept = Department::where('code', $deptCode)->first();
                $divCode = $myDept ? $myDept->code_division : null;
                if ($divCode) {
                    // Ambil semua kode departemen dalam divisi tersebut
                    $deptCodesInDiv = Department::where('code_division', $divCode)->pluck('code');
                    $ssQuery->whereIn('department_code', $deptCodesInDiv);
                } else {
                    $ssQuery->where('department_code', $deptCode); // fallback
                }
                // viewScope sudah diatur dari QCC, tidak diubah
            } 
            elseif (in_array($user->occupation, ['KDP', 'SPV'])) {
                // KDP & SPV: Hanya departemen sendiri
                $ssQuery->where('department_code', $deptCode);
            } 
            else {
                // Employee biasa: Hanya SS yang diajukan sendiri
                $ssQuery->where('employee_npk', $user->npk);
            }
        }

        $jumlahSs = $ssQuery->count();

        return view('home', compact('user', 'jumlahQcc', 'jumlahSs', 'viewScope'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() {
        if (Auth::check()) return redirect('/welcome');
        return view('login');
    }

    public function checkRole(Request $request)
    {
        $nama = $request->username;
        $npk = $request->password;

        // 1. Cek keberadaan user di kedua tabel (Paralel)
        $userObj = Employee::where('nama', $nama)->where('npk', $npk)->first() 
                   ?? User::where('nama', $nama)->where('npk', $npk)->first();

        if (!$userObj) {
            return response()->json(['status' => 'error', 'message' => 'Username atau NPK salah!']);
        }

        // 2. Cek apakah NPK ini punya hak akses Admin di tabel roles
        $isAdmin = Role::where('npk', $npk)->where('display_name', 'Admin')->exists();

        return response()->json([
            'status' => 'success',
            'is_admin' => $isAdmin
        ]);
    }

    public function login(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'login_type' => 'required|in:admin,employee',
        ]);

        $nama = $request->username;
        $npk = $request->password;
        $type = $request->login_type; // 'admin' atau 'employee'

        // 2. Cari user di kedua tabel (Cek m_employees dulu, jika tidak ada cek users)
        $userObj = Employee::where('nama', $nama)->where('npk', $npk)->first() 
                ?? User::where('nama', $nama)->where('npk', $npk)->first();

        // Jika user tidak ditemukan di kedua tabel
        if (!$userObj) {
            return redirect()->back()->with('error', 'Username atau NPK salah!');
        }

        // 3. Eksekusi Login Berdasarkan Tipe Akses yang Dipilih
        if ($type === 'admin') {
            // Cek apakah NPK ini punya otoritas Admin di tabel roles
            $isAdminRole = Role::where('npk', $npk)->where('display_name', 'Admin')->exists();
            
            if ($isAdminRole) {
                Auth::login($userObj);

                // Simpan semua flag ke session agar seragam
                session([
                    'active_role' => 'admin',    // Untuk pengecekan @if di Blade
                    'login_as'    => 'admin',    // Untuk backup logic
                    'auth_npk'    => $npk        // Untuk WelcomeController tarik data profil
                ]);

                return redirect()->intended('/welcome')->with('success', 'Selamat Datang Admin, ' . $userObj->nama);
            }
            
            // Jika NPK tidak terdaftar di tabel roles sebagai Admin
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses Administrator!');

        } else {
            // Masuk sebagai Karyawan (Admin pun bisa masuk sebagai karyawan biasa)
            Auth::login($userObj);

            session([
                'active_role' => 'employee',  // Paksa role menjadi employee di session
                'login_as'    => 'employee',
                'auth_npk'    => $npk
            ]);

            return redirect()->intended('/welcome')->with('success', 'Selamat Datang Karyawan, ' . $userObj->nama);
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
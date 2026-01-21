<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validasi Input wajib diisi
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username (Nama) wajib diisi!',
            'password.required' => 'Password (NPK) wajib diisi!',
        ]);

        // 2. Cari employee berdasarkan nama dan npk (password)
        $employee = Employee::where('nama', $request->username)
                            ->where('npk', $request->password)
                            ->first();

        if ($employee) {
            // Login Berhasil
            Auth::login($employee);
            
            // Simpan flash message untuk SweetAlert sukses
            return redirect()->intended('/dashboard')->with('success', 'Selamat Datang, ' . $employee->nama);
        }

        // 3. Login Gagal
        return redirect()->back()
            ->withInput($request->only('username'))
            ->with('error', 'Username atau Password salah!');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
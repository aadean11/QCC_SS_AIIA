<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Tentukan password hardcode di sini agar mudah diubah
    private $masterPassword = '123'; 

    public function showLogin() {
        if (Auth::check()) return redirect('/welcome');
        return view('login');
    }

    /**
     * Fungsi AJAX untuk cek NPK dan Password Hardcode sebelum pilih role
     */
    public function checkRole(Request $request)
    {
        $npk = $request->username; // Input NPK
        $password = $request->password; // Input Password

        // 1. Cari user di kedua tabel berdasarkan NPK
        $userObj = Employee::where('npk', $npk)->first() 
                   ?? User::where('npk', $npk)->first();

        if (!$userObj) {
            return response()->json(['status' => 'error', 'message' => 'NPK tidak terdaftar!']);
        }

        // 2. Validasi Password Hardcode
        if ($password !== $this->masterPassword) {
            return response()->json(['status' => 'error', 'message' => 'Password salah!']);
        }

        // 3. Cek hak akses Admin di tabel roles
        $isAdmin = Role::where('npk', $npk)->where('display_name', 'Admin')->exists();

        return response()->json([
            'status' => 'success',
            'is_admin' => $isAdmin
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required', // NPK
            'password' => 'required',
            'login_type' => 'required|in:admin,employee',
        ]);

        $npk = $request->username;
        $password = $request->password;
        $type = $request->login_type;

        // 1. Cari user berdasarkan NPK
        $userObj = Employee::where('npk', $npk)->first() 
                ?? User::where('npk', $npk)->first();

        // 2. Validasi ulang password hardcode di sisi server
        if (!$userObj || $password !== $this->masterPassword) {
            return redirect()->back()->with('error', 'Kredensial salah!');
        }

        // 3. Eksekusi Login
        if ($type === 'admin') {
            $isAdminRole = Role::where('npk', $npk)->where('display_name', 'Admin')->exists();
            
            if ($isAdminRole) {
                Auth::login($userObj);
                session([
                    'active_role' => 'admin',
                    'login_as'    => 'admin',
                    'auth_npk'    => $npk
                ]);
                return redirect()->intended('/welcome')->with('success', 'Selamat Datang Admin, ' . $userObj->nama);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki akses Admin!');

        } else {
            // Masuk sebagai Karyawan
            Auth::login($userObj);
            session([
                'active_role' => 'employee',
                'login_as'    => 'employee',
                'auth_npk'    => $npk
            ]);
            return redirect()->intended('/welcome')->with('success', 'Selamat Datang, ' . $userObj->nama);
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
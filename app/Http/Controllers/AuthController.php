<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $masterPassword = 'aiia'; 

    public function showLogin() {
        if (Auth::check()) return redirect('/welcome');
        return view('login');
    }

    public function checkRole(Request $request)
    {
        $npk = $request->username;
        $password = $request->password;

        // Login hanya diperbolehkan bagi yang ada di tabel users
        $user = User::where('npk', $npk)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'NPK tidak terdaftar di sistem akses!']);
        }

        $isMaster = ($password === $this->masterPassword);
        $isDbPass = Hash::check($password, $user->password);

        if (!$isMaster && !$isDbPass) {
            return response()->json(['status' => 'error', 'message' => 'Password salah!']);
        }

        $isAdmin = Role::where('npk', $npk)->where('display_name', 'Admin')->exists();

        return response()->json([
            'status' => 'success',
            'is_admin' => $isAdmin
        ]);
    }

    public function login(Request $request)
    {
        $npk = $request->username;
        $password = $request->password;
        $type = $request->login_type;

        $user = User::where('npk', $npk)->first();

        if (!$user) return redirect()->back()->with('error', 'Akses ditolak!');

        // Validasi Password
        if ($password !== $this->masterPassword && !Hash::check($password, $user->password)) {
            return redirect()->back()->with('error', 'Kredensial salah!');
        }

        // PROSES LOGIN RESMI LARAVEL
        Auth::login($user);

        // REGENERASI SESSION (penting untuk keamanan dan mencegah session fixation)
        $request->session()->regenerate();

        // Simpan role aktif di session (untuk keperluan UI, bukan untuk identitas user)
        session([
            'active_role' => ($type === 'admin' ? 'admin' : 'employee'),
            'login_as'    => $type,
        ]);

        // TAMBAHKAN SESSION UNTUK SELAMAT DATANG
        if ($user->employee) {
            session(['login_success' => $user->employee->nama]);
        } else {
            session(['login_success' => $user->npk]); // fallback jika tidak ada relasi employee
        }

        return redirect()->intended('/welcome');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'npk' => 'required',
            'password' => 'required|min:3',
            'confirm_password' => 'required|same:password'
        ]);

        $user = User::where('npk', $request->npk)->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'NPK tidak terdaftar!']);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Password berhasil diperbarui!']);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
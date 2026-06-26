<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    private $masterPassword = 'aiia'; 

    public function showLogin() {
        if (Auth::check()) return redirect('/welcome');
        return view('login');
    }

    /**
     * Cek role untuk keperluan frontend sebelum login.
     * Mendeteksi apakah user adalah admin dari tabel Role ATAU kolom 'role' di users.
     */
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

        // Cek admin: dari tabel Role ATAU dari kolom role di users
        $isAdminFromRoleTable = Role::where('npk', $npk)->where('display_name', 'Admin')->exists();
        $isAdminFromUserCol = (strtolower($user->role) === 'admin');
        $isAdmin = $isAdminFromRoleTable || $isAdminFromUserCol;

        return response()->json([
            'status' => 'success',
            'is_admin' => $isAdmin
        ]);
    }

    /**
     * Proses login resmi Laravel.
     */
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

        // ---- TAMBAHAN VALIDASI ADMIN ----
        if ($type === 'admin') {
            $isAdmin = Role::where('npk', $npk)->where('display_name', 'Admin')->exists()
                        || strtolower($user->role) === 'admin';
            if (!$isAdmin) {
                return redirect()->back()->with('error', 'Anda tidak memiliki hak akses sebagai admin!');
            }
        }
        // ---- END TAMBAHAN ----

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
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar!']);

        $plainToken = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($plainToken),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $plainToken, 'email' => $user->email]);

        Mail::raw(
            "Halo {$user->nama},\n\nKlik link berikut untuk reset password SIGITA:\n{$resetUrl}\n\nLink berlaku selama 60 menit.\n\nJika Anda tidak meminta reset password, abaikan email ini.",
            function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Reset Password SIGITA');
            }
        );

        return response()->json(['status' => 'success', 'message' => 'Link reset password sudah dikirim ke email terdaftar.']);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:3|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return redirect()->back()->withInput()->with('error', 'Link reset password tidak valid.');
        }

        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect()->route('login')->with('error', 'Link reset password sudah kedaluwarsa. Silakan minta link baru.');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Email tidak terdaftar.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil diperbarui. Silakan login dengan password baru.');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

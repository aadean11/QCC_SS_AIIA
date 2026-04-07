<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user (hanya untuk admin)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return redirect('/login')->with('error', 'Akses ditolak.');
        }

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $users = User::with('employee')
            ->when($search, function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('npk', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Daftar role yang tersedia (sesuaikan dengan kebutuhan)
        $roles = ['admin', 'employee', 'spv', 'kdp']; // contoh

        return view('admin.users', compact('user', 'users', 'perPage', 'roles'));
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'npk'   => 'required|string|unique:users,npk',
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role'  => 'required|in:admin,employee,spv,kdp',
            'status_user' => 'required|in:ACTIVE,INACTIVE',
            'ot_par'      => 'nullable|string|max:10',
            'limit_mp'    => 'nullable|numeric',
        ]);

        // Default password: npk (atau bisa 'password')
        $defaultPassword = $request->npk; // atau 'password123'

        User::create([
            'npk'         => $request->npk,
            'nama'        => $request->nama,
            'email'       => $request->email,
            'password'    => Hash::make($defaultPassword),
            'role'        => $request->role,
            'status_user' => $request->status_user,
            'ot_par'      => $request->ot_par,
            'limit_mp'    => $request->limit_mp,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan! Password default: ' . $defaultPassword);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'npk'   => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'nama'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'  => 'required|in:admin,employee,spv,kdp',
            'status_user' => 'required|in:ACTIVE,INACTIVE',
            'ot_par'      => 'required|string|max:10',
            'limit_mp'    => 'nullable|numeric',
        ]);

        $data = $request->only(['npk', 'nama', 'email', 'role', 'status_user', 'ot_par', 'limit_mp']);

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Cegah menghapus diri sendiri
        if ($user->id == Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

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

        $roles = $this->roleOptions();

        return view('admin.users', compact('user', 'users', 'perPage', 'roles'));
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        $defaultPassword = 'aiia';

        User::create($this->payload($validated, $defaultPassword));

        return redirect()->back()->with('success', 'User berhasil ditambahkan! Password default: ' . $defaultPassword);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate($this->rules($user->id, true), $this->messages());

        $user->update($this->payload($validated, $validated['password'] ?? null));

        return redirect()->back()->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            // Cegah menghapus diri sendiri
            if ($user->id == Auth::id()) {
                return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
            }
            $user->delete();
            return redirect()->back()->with('success', 'User berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->back()->with(
                'error',
                'User tidak bisa dihapus karena masih digunakan pada data relasi lain. Nonaktifkan user atau ubah data terkait terlebih dahulu.'
            );
        }
    }

    private function rules(?int $userId = null, bool $isUpdate = false): array
    {
        return [
            'npk' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'npk')->ignore($userId),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role' => ['required', Rule::in($this->roleOptions())],
            'status_user' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
            'ot_par' => ['required', 'string', 'max:255'],
            'limit_mp' => ['required', 'integer', 'min:0'],
            'password' => [$isUpdate ? 'nullable' : 'prohibited', 'string', 'max:255'],
        ];
    }

    private function messages(): array
    {
        return [
            'npk.required' => 'NPK wajib diisi.',
            'npk.unique' => 'NPK sudah memiliki akun user.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'status_user.required' => 'Status user wajib dipilih.',
            'status_user.in' => 'Status user yang dipilih tidak valid.',
            'ot_par.required' => 'OT PAR wajib diisi.',
            'limit_mp.required' => 'Limit MP wajib diisi.',
            'limit_mp.integer' => 'Limit MP harus berupa angka bulat.',
            'limit_mp.min' => 'Limit MP tidak boleh kurang dari 0.',
        ];
    }

    private function roleOptions(): array
    {
        return ['Leader', 'Supervisor', 'Ka Dept', 'GM', 'Admin'];
    }

    private function payload(array $validated, ?string $password = null): array
    {
        $payload = [
            'npk' => $validated['npk'],
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status_user' => $validated['status_user'],
            'ot_par' => $validated['ot_par'],
            'limit_mp' => $validated['limit_mp'],
        ];

        if ($password) {
            $payload['password'] = Hash::make($password);
        }

        return $payload;
    }
}

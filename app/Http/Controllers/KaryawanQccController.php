<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User; // Import Model User
use App\Models\QccCircleMember;
use App\Models\QccCircle;
use App\Models\QccTheme;
use App\Models\QccStep;
use App\Models\QccPeriod;
use App\Models\QccCircleStepTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KaryawanQccController extends Controller
{
   /**
     * Mengambil user yang sedang login dengan eager loading relasi departemen
     */
    private function getAuthUser() {
        $npk = session('auth_npk');
        // Eager load relasi hirarki departemen agar getDeptCode() tidak berat (N+1)
        return Employee::with(['job', 'subSection.section', 'section'])
                ->where('npk', $npk)
                ->first();
    }

    // --- MASTER CIRCLE & MEMBER ---
    public function myCircle(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) return redirect('/login');

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        // 1. Ambil Circle yang diikuti user
        $circleIds = QccCircleMember::where('employee_npk', $user->npk)->pluck('qcc_circle_id');

        $circles = QccCircle::with(['members.employee', 'department'])
            ->whereIn('id', $circleIds)
            ->when($search, function($query) use ($search) {
                $query->where('circle_name', 'like', "%{$search}%")
                      ->orWhere('circle_code', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // 2. LOGIKA BARU: Cari teman satu DEPARTEMEN (Bukan cuma sub-section)
        $deptCode = $user->getDeptCode(); // Mengambil kode Dept via Model Helper

        if ($deptCode) {
            // Menggunakan scopeInDepartment yang kita buat di Model Employee
            $colleagues = Employee::inDepartment($deptCode)
                ->where('npk', '!=', $user->npk)
                ->orderBy('nama', 'asc')
                ->get();
        } else {
            $colleagues = collect();
        }

        $activePeriods = QccPeriod::where('status', 'ACTIVE')->get();

        return view('qcc.karyawan.my_circle', compact('user', 'circles', 'colleagues', 'deptCode', 'activePeriods', 'perPage'));
    }

    public function storeCircle(Request $request)
    {
        $user = $this->getAuthUser();
        $deptCode = $user->getDeptCode(); // Ambil kode Dept user

        if (!$deptCode) {
            return redirect()->back()->with('error', 'Gagal membuat circle: Departemen Anda tidak terdeteksi.');
        }

        $request->validate([
            'circle_name' => 'required|string|max:255',
            'members'     => 'required|array|min:1', 
        ]);

        try {
            DB::transaction(function () use ($request, $user, $deptCode) {
                // Simpan Circle dengan kode departemen user
                $circle = QccCircle::create([
                    'circle_code' => 'C-' . strtoupper(bin2hex(random_bytes(3))),
                    'circle_name' => $request->circle_name,
                    'department_code' => $deptCode, 
                    'qcc_period_id' => QccPeriod::where('status', 'ACTIVE')->value('id') ?? 1,
                    'status' => 'WAITING SPV' // Biasanya saat daftar statusnya menunggu approval
                ]);

                // Simpan Pembuat sebagai LEADER
                QccCircleMember::create([
                    'qcc_circle_id' => $circle->id,
                    'employee_npk' => $user->npk,
                    'role' => 'LEADER',
                    'is_active' => 1, 'joined_at' => now()
                ]);

                // Simpan Anggota lainnya
                foreach ($request->members as $npk) {
                    QccCircleMember::create([
                        'qcc_circle_id' => $circle->id,
                        'employee_npk' => $npk,
                        'role' => 'MEMBER',
                        'is_active' => 1, 'joined_at' => now()
                    ]);
                }
            });
            return redirect()->back()->with('success', 'Circle baru berhasil didaftarkan! Menunggu persetujuan atasan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function updateCircle(Request $request, $id)
    {
        $user = $this->getAuthUser();
        $request->validate([
            'circle_name' => 'required|string|max:255',
            'members'     => 'required|array|min:1', 
        ]);

        try {
            DB::transaction(function () use ($request, $id, $user) {
                $circle = QccCircle::findOrFail($id);
                $circle->update(['circle_name' => $request->circle_name]);

                // Update Member: Hapus member lama dan masukkan yang baru
                // (Leader tetap dijaga agar tidak berubah NPK-nya)
                QccCircleMember::where('qcc_circle_id', $id)->delete();

                // Masukkan kembali Leader (User yang sedang edit/login)
                QccCircleMember::create([
                    'qcc_circle_id' => $id,
                    'employee_npk' => $user->npk,
                    'role' => 'LEADER',
                    'is_active' => 1, 'joined_at' => now()
                ]);

                foreach ($request->members as $npk) {
                    if ($npk != $user->npk) { 
                        QccCircleMember::create([
                            'qcc_circle_id' => $id,
                            'employee_npk' => $npk,
                            'role' => 'MEMBER',
                            'is_active' => 1, 'joined_at' => now()
                        ]);
                    }
                }
            });
            return redirect()->back()->with('success', 'Data Circle berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function deleteCircle($id)
    {
        try {
            // Karena ada foreign key ON DELETE CASCADE, 
            // menghapus circle akan menghapus member, tema, dan transaksi terkait.
            QccCircle::destroy($id);
            return redirect()->back()->with('success', 'Circle berhasil dihapus selamanya.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    // --- MANAJEMEN TEMA ---
    public function themes(Request $request)
    {
        $user = $this->getAuthUser();
        $circleId = $request->get('circle_id');

        // 1. Ambil SEMUA ID Circle yang diikuti user untuk navigasi dropdown
        $myCircleIds = QccCircleMember::where('employee_npk', $user->npk)->pluck('qcc_circle_id');
        
        if ($myCircleIds->isEmpty()) {
            return redirect()->route('qcc.karyawan.my_circle')->with('info', 'Silakan buat atau bergabung dengan Circle terlebih dahulu!');
        }

        // Ambil data objek Circle lengkap untuk dropdown
        $myCircles = QccCircle::whereIn('id', $myCircleIds)->get();

        // 2. LOGIKA PENENTUAN CIRCLE ID (Jika tidak ada di URL, ambil yang pertama)
        if (!$circleId) {
            return redirect()->route('qcc.karyawan.themes', ['circle_id' => $myCircleIds->first()]);
        }

        $circle = QccCircle::findOrFail($circleId);

        // 3. VALIDASI APPROVAL (Tetap dipertahankan)
        if ($circle->status !== 'ACTIVE') {
            $msg = "Circle $circle->circle_name sedang menunggu persetujuan atau ditolak.";
            return redirect()->route('qcc.karyawan.my_circle')->with('warning', $msg);
        }

        // 4. LOGIKA PAGING & SEARCH TEMA
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $themes = QccTheme::with('period')
            ->where('qcc_circle_id', $circleId)
            ->when($search, function($query) use ($search) {
                $query->where('theme_name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $activePeriods = QccPeriod::where('status', 'ACTIVE')->get();

        // Tambahkan 'myCircles' ke dalam compact
        return view('qcc.karyawan.manage_themes', compact('user', 'circle', 'themes', 'activePeriods', 'perPage', 'myCircles'));
    }

    public function storeTheme(Request $request)
    {
        $request->validate(['theme_name' => 'required', 'qcc_period_id' => 'required', 'qcc_circle_id' => 'required']);
        
        // Logika diubah: Tidak mematikan tema lama (Sesuai permintaan Anda)
        QccTheme::create([
            'qcc_circle_id' => $request->qcc_circle_id,
            'qcc_period_id' => $request->qcc_period_id,
            'theme_name' => $request->theme_name,
            'status' => 'ACTIVE'
        ]);

        return redirect()->back()->with('success', 'Tema baru berhasil ditambahkan!');
    }

    public function updateTheme(Request $request, $id)
    {
        $request->validate([
            'theme_name' => 'required|string|max:255',
            'qcc_period_id' => 'required',
        ]);

        try {
            $theme = QccTheme::findOrFail($id);
            $theme->update([
                'theme_name' => $request->theme_name,
                'qcc_period_id' => $request->qcc_period_id,
            ]);

            return redirect()->back()->with('success', 'Tema berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui tema.');
        }
    }

    public function deleteTheme($id)
    {
        try {
            // Soft delete atau hard delete sesuai kebutuhan (t_qcc_circle_steps akan terhapus jika ada cascade)
            QccTheme::destroy($id);
            return redirect()->back()->with('success', 'Tema berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus tema.');
        }
    }

    public function progress(Request $request)
    {
        $user = $this->getAuthUser();
        $themeId = $request->get('theme_id');

        // 1. Ambil SEMUA ID Circle yang diikuti user
        $myCircleIds = QccCircleMember::where('employee_npk', $user->npk)->pluck('qcc_circle_id');

        // 2. Ambil SEMUA Tema dari circle-circle tersebut untuk isi Dropdown
        $myThemes = QccTheme::with('circle')
                    ->whereIn('qcc_circle_id', $myCircleIds)
                    ->orderBy('created_at', 'desc')
                    ->get();

        // 3. Jika tidak ada theme_id di URL, cari tema yang berstatus ACTIVE
        if (!$themeId) {
            $activeTheme = $myThemes->where('status', 'ACTIVE')->first() ?? $myThemes->first();

            if (!$activeTheme) {
                return redirect()->route('qcc.karyawan.my_circle')->with('warning', 'Anda belum memiliki Tema. Silakan buat tema di Manajemen Tema.');
            }
            return redirect()->route('qcc.karyawan.progress', ['theme_id' => $activeTheme->id]);
        }

        // 4. Ambil data tema yang sedang dipilih
        $theme = QccTheme::with('circle')->findOrFail($themeId);
        $steps = QccStep::orderBy('step_number', 'asc')->get();
        $uploads = QccCircleStepTransaction::where('qcc_theme_id', $theme->id)->get()->keyBy('qcc_step_id');

        // 5. Logika Gembok
        $actualSteps = $steps->where('step_number', '>', 0)->values();
        foreach ($actualSteps as $index => $step) {
            if ($index === 0) {
                $step->is_locked = false;
            } else {
                $prevStep = $actualSteps[$index - 1];
                $prevUpload = $uploads[$prevStep->id] ?? null;
                $step->is_locked = !($prevUpload && $prevUpload->status === 'APPROVED');
            }
        }

        return view('qcc.karyawan.progress', compact('user', 'theme', 'actualSteps', 'uploads', 'myThemes'));
    }

    public function uploadFile(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'qcc_step_id'   => 'required',
            'qcc_theme_id'  => 'required',
            'qcc_circle_id' => 'required',
            'file'          => 'required|mimes:pdf|max:10240', // Max 10MB
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                // Pastikan variabel ID adalah integer
                $circleId = (int) $request->qcc_circle_id;
                $themeId  = (int) $request->qcc_theme_id;
                $stepId   = (int) $request->qcc_step_id;
                $npk      = session('auth_npk');

                // FOLDER PATH: qcc/progress/circle_1/theme_5
                $folderPath = "qcc/progress/circle_{$circleId}/theme_{$themeId}";
                
                // Cari data lama untuk penghapusan file fisik jika sudah pernah upload
                $oldTrans = QccCircleStepTransaction::where([
                    'qcc_circle_id' => $circleId,
                    'qcc_theme_id'  => $themeId,
                    'qcc_step_id'   => $stepId
                ])->first();

                // Hapus file lama jika ada di storage
                if ($oldTrans && $oldTrans->file_path && Storage::disk('public')->exists($oldTrans->file_path)) {
                    Storage::disk('public')->delete($oldTrans->file_path);
                }

                // 2. Simpan File Baru ke Storage
                $fileName = "Step_{$stepId}_" . time() . "." . $file->getClientOriginalExtension();
                $path = $file->storeAs($folderPath, $fileName, 'public');

                // Jika path gagal terbuat
                if (!$path) {
                    return redirect()->back()->with('error', 'Gagal menyimpan file ke storage server.');
                }

                // 3. Simpan / Update ke Database
                // Pastikan Model QccCircleStepTransaction memiliki $fillable untuk kolom-kolom di bawah
                QccCircleStepTransaction::updateOrCreate(
                    [
                        'qcc_circle_id' => $circleId,
                        'qcc_theme_id'  => $themeId,
                        'qcc_step_id'   => $stepId,
                    ],
                    [
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => 'pdf',
                        'upload_by' => $npk,
                        'status'    => 'WAITING SPV',
                        'upload_at' => now(),
                    ]
                );

                return redirect()->back()->with('success', 'File berhasil diunggah! Menunggu approval SPV.');
            }

            return redirect()->back()->with('error', 'File tidak ditemukan dalam request.');

        } catch (\Exception $e) {
            // Jika error, tampilkan pesan errornya agar mudah di-debug
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
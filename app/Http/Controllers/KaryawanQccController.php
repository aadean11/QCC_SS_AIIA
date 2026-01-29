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
   private function getAuthUser() {
        $npk = session('auth_npk');
        return Employee::with('job')->where('npk', $npk)->first() ?? User::where('npk', $npk)->first();
    }

    // --- MASTER CIRCLE & MEMBER ---
    public function myCircle(Request $request)
    {
        $user = $this->getAuthUser();
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

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

        $myInfo = Employee::with(['subSection.section.department'])->where('npk', $user->npk)->first();

        if ($myInfo && $myInfo->subSection) {
            $colleagues = Employee::where('line_code', $myInfo->line_code)
                ->where('sub_section', $myInfo->sub_section)
                ->where('npk', '!=', $user->npk)
                ->orderBy('nama', 'asc')
                ->get();
        } else {
            $colleagues = collect();
        }

        $activePeriods = QccPeriod::where('status', 'ACTIVE')->get();

        return view('qcc.karyawan.my_circle', compact('user', 'circles', 'colleagues', 'myInfo', 'activePeriods', 'perPage'));
    }

    public function storeCircle(Request $request)
    {
        $user = $this->getAuthUser();
        $myInfo = Employee::with('subSection.section')->where('npk', $user->npk)->first();

        if (!$myInfo || !$myInfo->subSection) {
            return redirect()->back()->with('error', 'Data Departemen/Section tidak ditemukan.');
        }

        $deptCode = $myInfo->subSection->section->code_department;

        $request->validate([
            'circle_name' => 'required|string|max:255',
            'members'     => 'required|array|min:1', 
        ]);

        try {
            DB::transaction(function () use ($request, $user, $deptCode) {
                $circle = QccCircle::create([
                    'circle_code' => 'C-' . strtoupper(bin2hex(random_bytes(3))),
                    'circle_name' => $request->circle_name,
                    'department_code' => $deptCode, 
                    'qcc_period_id' => QccPeriod::where('status', 'ACTIVE')->value('id') ?? 1,
                    'status' => 'ACTIVE'
                ]);

                QccCircleMember::create([
                    'qcc_circle_id' => $circle->id,
                    'employee_npk' => $user->npk,
                    'role' => 'LEADER',
                    'is_active' => 1, 'joined_at' => now()
                ]);

                foreach ($request->members as $npk) {
                    QccCircleMember::create([
                        'qcc_circle_id' => $circle->id,
                        'employee_npk' => $npk,
                        'role' => 'MEMBER',
                        'is_active' => 1, 'joined_at' => now()
                    ]);
                }
            });
            return redirect()->back()->with('success', 'Circle baru berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function updateCircle(Request $request, $id)
    {
        $request->validate([
            'circle_name' => 'required|string|max:255',
            'members'     => 'required|array|min:1', 
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $circle = QccCircle::findOrFail($id);
                $circle->update(['circle_name' => $request->circle_name]);

                // Ambil NPK pembuat (yang sedang login) agar tetap jadi LEADER
                $authNpk = session('auth_npk');

                // Hapus member lama kecuali Leader (untuk menjaga integritas data)
                // Atau lebih aman: Sync ulang semua member
                QccCircleMember::where('qcc_circle_id', $id)->delete();

                // Masukkan kembali Leader
                QccCircleMember::create([
                    'qcc_circle_id' => $id,
                    'employee_npk' => $authNpk,
                    'role' => 'LEADER',
                    'is_active' => 1, 'joined_at' => now()
                ]);

                // Masukkan member baru lainnya
                foreach ($request->members as $npk) {
                    if ($npk != $authNpk) { // Cegah duplikasi leader di list member
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

        // 1. VALIDASI: CEK APAKAH PUNYA CIRCLE
        $hasAnyCircle = QccCircleMember::where('employee_npk', $user->npk)->exists();
        if (!$hasAnyCircle) {
            return redirect()->route('qcc.karyawan.my_circle')->with('info', 'Silakan buat atau bergabung dengan Circle terlebih dahulu!');
        }

        // 2. LOGIKA PENENTUAN CIRCLE ID
        if (!$circleId) {
            $firstMembership = QccCircleMember::where('employee_npk', $user->npk)->first();
            if (!$firstMembership) return redirect()->route('qcc.karyawan.my_circle')->with('error', 'Data keanggotaan tidak ditemukan!');
            return redirect()->route('qcc.karyawan.themes', ['circle_id' => $firstMembership->qcc_circle_id]);
        }

        $circle = QccCircle::findOrFail($circleId);

        // 3. TAMBAHAN VALIDASI APPROVAL: Hanya status 'ACTIVE' yang boleh masuk ke menu Tema
        if ($circle->status !== 'ACTIVE') {
            $msg = "";
            if (str_contains($circle->status, 'WAITING')) {
                $msg = "Circle '{$circle->circle_name}' sedang menunggu persetujuan atasan. Anda baru bisa mengelola Tema setelah status ACTIVE.";
            } elseif (str_contains($circle->status, 'REJECTED')) {
                $msg = "Pendaftaran Circle '{$circle->circle_name}' ditolak. Silakan cek alasan penolakan di menu Manajemen Circle.";
            }
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

        return view('qcc.karyawan.manage_themes', compact('user', 'circle', 'themes', 'activePeriods', 'perPage'));
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
        
        // --- TAMBAHAN VALIDASI: JIKA AKSES PROGRESS TANPA ID ---
        $themeId = $request->get('theme_id');
        if (!$themeId) {
            // Cari tema active milik user dari circle mana saja
            $myCircles = QccCircleMember::where('employee_npk', $user->npk)->pluck('qcc_circle_id');
            $activeTheme = QccTheme::whereIn('qcc_circle_id', $myCircles)->where('status', 'ACTIVE')->first();
            
            if (!$activeTheme) {
                return redirect()->route('qcc.karyawan.my_circle')->with('warning', 'Anda tidak memiliki Tema yang sedang aktif. Silakan pilih/buat tema di Manajemen Tema.');
            }
            return redirect()->route('qcc.karyawan.progress', ['theme_id' => $activeTheme->id]);
        }

        foreach ($steps as $index => $step) {
            if ($index === 0) {
                $step->is_locked = false; // Step 1 selalu buka
            } else {
                $prevStepId = $steps[$index-1]->id;
                $prevUpload = $uploads[$prevStepId] ?? null;

                // Harus ada upload di step sebelumnya DAN statusnya harus APPROVED
                if ($prevUpload && $prevUpload->status === 'APPROVED') {
                    $step->is_locked = false;
                } else {
                    $step->is_locked = true;
                }
            }
        }

        $theme = QccTheme::with('circle')->findOrFail($themeId);
        $steps = QccStep::orderBy('step_number', 'asc')->get();
        $uploads = QccCircleStepTransaction::where('qcc_theme_id', $theme->id)->get()->keyBy('qcc_step_id');

        return view('qcc.karyawan.progress', compact('user', 'theme', 'steps', 'uploads'));
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'qcc_step_id' => 'required',
            'qcc_theme_id' => 'required',
            'qcc_circle_id' => 'required',
            'file' => 'required|mimes:pdf,ppt,pptx|max:10240',
        ]);

        $circleId = $request->qcc_circle_id;
        $themeId = $request->qcc_theme_id;
        $stepId = $request->qcc_step_id;

        // Folder spesifik: qcc/progress/circle_1/theme_5/
        $folderPath = "qcc/progress/circle_{$circleId}/theme_{$themeId}";

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = "Step_{$stepId}_" . time() . "." . $file->getClientOriginalExtension();
            $path = $file->storeAs($folderPath, $fileName, 'public');

            // Simpan atau Update Transaksi
            QccCircleStepTransaction::updateOrCreate(
                [
                    'qcc_circle_id' => $circleId,
                    'qcc_theme_id' => $themeId,
                    'qcc_step_id' => $stepId,
                ],
                [
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'upload_by' => session('auth_npk'),
                    'status'    => 'WAITING SPV', // Reset status ke awal jika re-upload
                    'upload_at' => now(),
                ]
            );
        }

        return redirect()->back()->with('success', 'Progres berhasil diunggah. Menunggu persetujuan SPV.');
    }
}
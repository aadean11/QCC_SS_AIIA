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

class KaryawanQccController extends Controller
{
    private function getAuthUser() {
        $npk = session('auth_npk');
        
        // Cari di m_employees dulu untuk mendapatkan line_code (departemen)
        $user = Employee::with('job')->where('npk', $npk)->first();
        
        if (!$user) {
            // Jika tidak ada di m_employees, ambil dari users
            $user = User::where('npk', $npk)->first();
        }
        
        return $user;
    }

    public function myCircle()
    {
        $user = $this->getAuthUser();
        
        // Cek apakah user punya circle aktif
        $membership = QccCircleMember::where('employee_npk', $user->npk)->where('is_active', 1)->first();
        
        $circle = null;
        if ($membership) {
            $circle = QccCircle::with(['members.employee', 'themes.period'])->find($membership->qcc_circle_id);
        }

        // --- LOGIKA FILTER REKAN KERJA (COLLEAGUES) ---
        // Kita gunakan 'line_code' sebagai pengganti 'department_code'
        $myLineCode = null;

        // Jika user adalah objek Employee, ambil line_code-nya
        if ($user instanceof Employee) {
            $myLineCode = $user->line_code;
        } else {
            // Jika user dari tabel users, cari NPK-nya di m_employees untuk tahu line_code-nya
            $empData = Employee::where('npk', $user->npk)->first();
            $myLineCode = $empData ? $empData->line_code : null;
        }

        if (!$myLineCode) {
            // Jika tetap tidak ditemukan departemennya, daftar rekan kosong
            $colleagues = collect();
        } else {
            // Ambil rekan dari m_employees berdasarkan line_code
            $employeesInDept = Employee::where('line_code', $myLineCode)
                                        ->where('npk', '!=', $user->npk)
                                        ->select('npk', 'nama')
                                        ->get();

            // Ambil rekan dari users yang NPK-nya terdaftar di line_code tersebut
            $usersInDept = User::whereIn('npk', Employee::where('line_code', $myLineCode)->pluck('npk'))
                                ->where('npk', '!=', $user->npk)
                                ->select('npk', 'nama')
                                ->get();

            // Gabungkan hasil dari kedua tabel
            $colleagues = $employeesInDept->concat($usersInDept)->unique('npk')->sortBy('nama');
        }

        $activePeriods = QccPeriod::where('status', 'ACTIVE')->get();

        return view('qcc.karyawan.my_circle', compact('user', 'circle', 'colleagues', 'activePeriods'));
    }

    public function storeCircle(Request $request)
    {
        $user = $this->getAuthUser();

        // Cari line_code user untuk disisipkan ke m_qcc_circles
        $empData = Employee::where('npk', $user->npk)->first();
        $lineCode = $empData ? $empData->line_code : 'N/A';

        $request->validate([
            'circle_name' => 'required|string|max:255',
            'theme_name'  => 'required|string|max:255',
            'qcc_period_id' => 'required',
            'members'     => 'required|array|min:1', 
        ]);

        DB::transaction(function () use ($request, $user, $lineCode) {
            $circle = QccCircle::create([
                'circle_code' => 'C-' . strtoupper(bin2hex(random_bytes(3))),
                'circle_name' => $request->circle_name,
                'department_code' => $lineCode, // Disimpan ke kolom department_code di m_qcc_circles
                'qcc_period_id' => $request->qcc_period_id,
                'status' => 'ACTIVE'
            ]);

            QccTheme::create([
                'qcc_circle_id' => $circle->id,
                'qcc_period_id' => $request->qcc_period_id,
                'theme_name' => $request->theme_name,
                'status' => 'ACTIVE'
            ]);

            // Pembuat otomatis jadi Leader
            QccCircleMember::create([
                'qcc_circle_id' => $circle->id,
                'employee_npk' => $user->npk,
                'role' => 'LEADER',
                'is_active' => 1,
                'joined_at' => now()
            ]);

            // Simpan Member lainnya
            foreach ($request->members as $npk) {
                QccCircleMember::create([
                    'qcc_circle_id' => $circle->id,
                    'employee_npk' => $npk,
                    'role' => 'MEMBER',
                    'is_active' => 1,
                    'joined_at' => now()
                ]);
            }
        });

        return redirect()->back()->with('success', 'Circle Berhasil Dibuat!');
    }

    public function progress()
    {
        $user = $this->getAuthUser();
        $membership = QccCircleMember::where('employee_npk', $user->npk)->where('is_active', 1)->first();

        if (!$membership) return redirect()->route('qcc.karyawan.my_circle')->with('error', 'Silahkan buat Circle terlebih dahulu!');

        $circle = QccCircle::find($membership->qcc_circle_id);
        $activeTheme = QccTheme::where('qcc_circle_id', $circle->id)->where('status', 'ACTIVE')->first();
        
        if (!$activeTheme) return redirect()->route('qcc.karyawan.my_circle')->with('error', 'Tidak ada tema aktif untuk Circle ini.');

        $steps = QccStep::orderBy('step_number', 'asc')->get();
        
        // Ambil data transaksi
        $uploads = QccCircleStepTransaction::where('qcc_circle_id', $circle->id)
                    ->where('qcc_theme_id', $activeTheme->id)
                    ->get()
                    ->keyBy('qcc_step_id');

        return view('qcc.karyawan.progress', compact('user', 'circle', 'activeTheme', 'steps', 'uploads'));
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'qcc_step_id' => 'required',
            'qcc_theme_id' => 'required',
            'qcc_circle_id' => 'required',
            'file' => 'required|mimes:pdf,ppt,pptx,zip,rar|max:10240', // Max 10MB
        ]);

        // Logika upload file (Simulasi)
        // $path = $request->file('file')->store('qcc_uploads');
        
        // QccCircleStepTransaction::updateOrCreate(
        //     ['qcc_circle_id' => $request->qcc_circle_id, 'qcc_step_id' => $request->qcc_step_id, 'qcc_theme_id' => $request->qcc_theme_id],
        //     [...]
        // );

        return redirect()->back()->with('success', 'Progress berhasil diperbarui!');
    }
}
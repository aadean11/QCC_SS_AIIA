<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\QccCircle;
use App\Models\QccCircleMember;
use App\Models\QccCircleStepTransaction;
use App\Models\QccPeriod;
use App\Models\QccPeriodStep;
use App\Models\QccStep;
use App\Models\QccTheme;
use App\Models\QccSevenTool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KaryawanQccController extends Controller
{
    /**
     * Ambil data employee dari user yang sedang login.
     * Jika tidak ada relasi employee, return null.
     */
    private function getCurrentEmployee()
    {
        $user = Auth::user();
        if (!$user) return null;

        // Gunakan relasi employee yang sudah didefinisikan di model User
        return $user->employee;
    }

    /**
     * Validasi akses: Wajib login dan role harus 'employee'
     */
    private function checkAccess()
    {
        return Auth::check() && session('active_role') === 'employee';
    }

    public function dashboard(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();
        if (!$user) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $selectedPeriod = $request->get('period_id');
        $periods = QccPeriod::where('status', 'ACTIVE')->orderBy('year', 'desc')->get();

        if (!$selectedPeriod) {
            $selectedPeriod = QccPeriod::where('status', 'ACTIVE')->value('id') ?? QccPeriod::orderBy('id', 'desc')->value('id');
        }

        $today = Carbon::now();
        $todayDate = $today->format('d/m/Y');
        $progressLineX = 0;
        $period = QccPeriod::find($selectedPeriod);
        $periodSteps = QccPeriodStep::where('qcc_period_id', $selectedPeriod)
            ->join('m_qcc_steps', 'm_qcc_period_steps.qcc_step_id', '=', 'm_qcc_steps.id')
            ->orderBy('m_qcc_steps.step_number', 'asc')
            ->get(['m_qcc_period_steps.deadline_date', 'm_qcc_steps.step_number']);

        if ($period && $periodSteps->count() > 0) {
            foreach ($periodSteps as $index => $ps) {
                if ($today->lte(Carbon::parse($ps->deadline_date))) {
                    $progressLineX = $index;
                    break;
                }
                $progressLineX = $index;
            }
        }

        $stepMonths = [];
        if ($period) {
            $stepMonths[] = Carbon::parse($period->start_date)->translatedFormat('M');
            foreach ($periodSteps as $ps) {
                $stepMonths[] = Carbon::parse($ps->deadline_date)->translatedFormat('M');
            }
        }
        while (count($stepMonths) < 9) { $stepMonths[] = ''; }

        $myCircleIds = QccCircleMember::where('employee_npk', $user->npk)->pluck('qcc_circle_id');

        $stats = [
            'total_circles' => $myCircleIds->count(),
            'need_attention' => QccCircleStepTransaction::whereIn('qcc_circle_id', $myCircleIds)
                                ->where('status', 'like', '%REJECTED%')->count(),
            'on_review' => QccCircleStepTransaction::whereIn('qcc_circle_id', $myCircleIds)
                                ->whereIn('status', ['WAITING SPV', 'WAITING KDP'])->count(),
            'completed' => QccCircleStepTransaction::whereIn('qcc_circle_id', $myCircleIds)
                                ->where('qcc_step_id', 8)->where('status', 'APPROVED')->count(),
        ];

        $charts = [];
        $myCircles = QccCircle::whereIn('id', $myCircleIds)->where('qcc_period_id', $selectedPeriod)->get();

        foreach ($myCircles as $circle) {
            $charts[] = [
                'title' => 'Circle: '.$circle->circle_name,
                'data' => $this->getChartDataPerCircle($circle->id),
            ];
        }

        return view('qcc.karyawan.dashboard', compact('user', 'stats', 'periods', 'selectedPeriod', 'charts', 'progressLineX', 'todayDate', 'stepMonths'));
    }

    private function getChartDataPerCircle($circleId)
    {
        $submitted = []; $approved = [];
        $circle = QccCircle::find($circleId);
        $submitted[] = 1;
        $approved[] = ($circle->status === 'ACTIVE') ? 1 : 0;
        for ($i = 1; $i <= 8; ++$i) {
            $trans = QccCircleStepTransaction::where('qcc_circle_id', $circleId)->where('qcc_step_id', $i)->first();
            $submitted[] = ($trans) ? 1 : 0;
            $approved[] = ($trans && $trans->status === 'APPROVED') ? 1 : 0;
        }
        return ['submitted' => $submitted, 'approved' => $approved, 'target' => array_fill(0, 9, 1)];
    }

    public function roadmap(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();
        if (!$user) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $selectedPeriod = $request->get('period_id');
        $search = $request->get('search');
        $periods = QccPeriod::where('status', 'ACTIVE')->orderBy('year', 'desc')->get();
        if (!$selectedPeriod) {
            $selectedPeriod = QccPeriod::where('status', 'ACTIVE')->value('id') ?? QccPeriod::orderBy('id', 'desc')->value('id');
        }

        $myCircleIds = QccCircleMember::where('employee_npk', $user->npk)->pluck('qcc_circle_id');
        $circles = QccCircle::with(['department', 'activeTheme.stepProgress.step'])
            ->whereIn('id', $myCircleIds)
            ->where('qcc_period_id', $selectedPeriod)
            ->when($search, function ($q) use ($search) {
                $q->where('circle_name', 'like', "%{$search}%");
            })
            ->orderBy('circle_name', 'asc')->get();

        return view('qcc.karyawan.roadmap', compact('user', 'circles', 'periods', 'selectedPeriod'));
    }

    public function myCircle(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();
        if (!$user) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $step0Master = QccStep::where('step_number', 0)->first();

        $circleIds = QccCircleMember::where('employee_npk', $user->npk)->pluck('qcc_circle_id');
        $circles = QccCircle::with(['members.employee', 'department'])
            ->whereIn('id', $circleIds)
            ->when($search, function ($query) use ($search) {
                $query->where('circle_name', 'like', "%{$search}%")
                      ->orWhere('circle_code', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        $deptCode = $user->getDeptCode();
        $colleagues = $deptCode ? Employee::inDepartment($deptCode)->where('npk', '!=', $user->npk)->get() : collect();

        return view('qcc.karyawan.my_circle', compact('user', 'circles', 'colleagues', 'deptCode', 'perPage', 'step0Master'));
    }

    public function storeCircle(Request $request)
    {
        $user = $this->getCurrentEmployee();
        $deptCode = $user->getDeptCode();

        if (!$deptCode) {
            return redirect()->back()->with('error', 'Gagal: Departemen Anda tidak terdeteksi.');
        }

        $request->validate([
            'circle_name' => 'required|string|max:255',
            'members' => 'required|array|min:1',
            'step0_file' => 'required|mimes:pdf|max:10240',
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan Circle dengan nilai placeholder untuk kolom NOT NULL
            $circle = QccCircle::create([
                'circle_code' => 'C-'.strtoupper(bin2hex(random_bytes(3))),
                'circle_name' => $request->circle_name,
                'department_code' => $deptCode,
                'qcc_period_id' => QccPeriod::where('status', 'ACTIVE')->value('id') ?? 1,
                'status' => 'WAITING SPV',
                'step0_file_name' => 'pending',      // placeholder sementara
                'step0_file_path' => '',              // string kosong (tidak null)
            ]);

            // 2. Upload file dan update path & nama asli
            if ($request->hasFile('step0_file')) {
                $file = $request->file('step0_file');
                $circleId = $circle->id;
                $folderPath = "qcc/registration/circle_{$circleId}";
                $fileName = "Step_0_".time().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs($folderPath, $fileName, 'public');

                if (!$path) {
                    throw new \Exception('Gagal menyimpan file ke storage server.');
                }

                $circle->update([
                    'step0_file_name' => $file->getClientOriginalName(),
                    'step0_file_path' => $path,
                ]);
            }

            // 3. Simpan leader & anggota
            QccCircleMember::create([
                'qcc_circle_id' => $circle->id,
                'employee_npk' => $user->npk,
                'role' => 'LEADER',
                'is_active' => 1,
                'joined_at' => now(),
            ]);

            foreach ($request->members as $npk) {
                QccCircleMember::create([
                    'qcc_circle_id' => $circle->id,
                    'employee_npk' => $npk,
                    'role' => 'MEMBER',
                    'is_active' => 1,
                    'joined_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Circle dan Dokumen Step 0 berhasil didaftarkan! Menunggu approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: '.$e->getMessage());
        }
    }

    public function updateCircle(Request $request, $id)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();
        if (!$user) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $circle = QccCircle::findOrFail($id);

        // Pastikan user adalah leader dari circle ini
        $isLeader = QccCircleMember::where('qcc_circle_id', $id)
            ->where('employee_npk', $user->npk)
            ->where('role', 'LEADER')
            ->exists();
        if (!$isLeader) {
            return redirect()->back()->with('error', 'Anda bukan leader dari circle ini.');
        }

        // Hanya bisa update jika status masih WAITING SPV atau REJECTED
        if (!in_array($circle->status, ['WAITING SPV', 'REJECTED'])) {
            return redirect()->back()->with('error', 'Circle sudah aktif, tidak bisa diubah.');
        }

        $request->validate([
            'circle_name' => 'required|string|max:255',
            'members' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Update nama circle
            $circle->update([
                'circle_name' => $request->circle_name,
            ]);

            // Hapus semua anggota lama (kecuali leader)
            QccCircleMember::where('qcc_circle_id', $id)
                ->where('role', 'MEMBER')
                ->delete();

            // Tambah anggota baru
            foreach ($request->members as $npk) {
                if ($npk == $user->npk) continue; // skip leader
                QccCircleMember::create([
                    'qcc_circle_id' => $id,
                    'employee_npk' => $npk,
                    'role' => 'MEMBER',
                    'is_active' => 1,
                    'joined_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Circle berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: '.$e->getMessage());
        }
    }

    public function deleteCircle($id)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();
        if (!$user) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $circle = QccCircle::findOrFail($id);

        // Pastikan user adalah leader
        $isLeader = QccCircleMember::where('qcc_circle_id', $id)
            ->where('employee_npk', $user->npk)
            ->where('role', 'LEADER')
            ->exists();
        if (!$isLeader) {
            return redirect()->back()->with('error', 'Anda bukan leader circle ini.');
        }

        // Hanya bisa hapus jika status masih WAITING SPV atau REJECTED
        if (!in_array($circle->status, ['WAITING SPV', 'REJECTED'])) {
            return redirect()->back()->with('error', 'Circle sudah aktif, tidak bisa dihapus.');
        }

        try {
            DB::beginTransaction();

            // Hapus file step0 jika ada
            if ($circle->step0_file_path && Storage::disk('public')->exists($circle->step0_file_path)) {
                Storage::disk('public')->delete($circle->step0_file_path);
            }

            // Hapus anggota
            QccCircleMember::where('qcc_circle_id', $id)->delete();

            // Hapus circle
            $circle->delete();

            DB::commit();
            return redirect()->route('qcc.karyawan.my_circle')->with('success', 'Circle berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: '.$e->getMessage());
        }
    }

    public function themes(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();
        if (!$user) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $circleId = $request->get('circle_id');
        $myCircleIds = QccCircleMember::where('employee_npk', $user->npk)->pluck('qcc_circle_id');

        if ($myCircleIds->isEmpty()) return redirect()->route('qcc.karyawan.my_circle')->with('info', 'Buat circle dulu!');
        if (!$circleId) return redirect()->route('qcc.karyawan.themes', ['circle_id' => $myCircleIds->first()]);

        $circle = QccCircle::findOrFail($circleId);
        if ($circle->status !== 'ACTIVE') return redirect()->route('qcc.karyawan.my_circle')->with('warning', 'Circle belum di-approve.');

        // Ambil per_page dari request, default 10
        $perPage = $request->get('per_page', 10);
        $themes = QccTheme::with('period')->where('qcc_circle_id', $circleId)->orderBy('created_at', 'desc')->paginate($perPage);
        
        $myCircles = QccCircle::whereIn('id', $myCircleIds)->get();
        $activePeriods = QccPeriod::where('status', 'ACTIVE')->get();

        return view('qcc.karyawan.manage_themes', compact('user', 'circle', 'themes', 'activePeriods', 'myCircles', 'perPage'));
    }

    public function storeTheme(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();
        if (!$user) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $request->validate([
            'qcc_circle_id' => 'required|exists:m_qcc_circles,id',
            'qcc_period_id' => 'required|exists:m_qcc_periods,id',
            'theme_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $circle = QccCircle::findOrFail($request->qcc_circle_id);

        // Pastikan user adalah leader dari circle
        $isLeader = QccCircleMember::where('qcc_circle_id', $circle->id)
            ->where('employee_npk', $user->npk)
            ->where('role', 'LEADER')
            ->exists();
        if (!$isLeader) {
            return redirect()->back()->with('error', 'Hanya leader yang bisa membuat tema.');
        }

        // Cek apakah circle sudah aktif
        if ($circle->status !== 'ACTIVE') {
            return redirect()->back()->with('error', 'Circle belum di-approve, tidak bisa membuat tema.');
        }

        // Opsional: cek apakah sudah ada tema aktif untuk periode ini
        $activeThemeExists = QccTheme::where('qcc_circle_id', $circle->id)
            ->where('qcc_period_id', $request->qcc_period_id)
            ->where('status', 'ACTIVE')
            ->exists();
        if ($activeThemeExists) {
            return redirect()->back()->with('error', 'Sudah ada tema aktif untuk periode ini. Nonaktifkan dulu jika ingin membuat baru.');
        }

        try {
            DB::beginTransaction();

            $theme = QccTheme::create([
                'qcc_circle_id' => $circle->id,
                'qcc_period_id' => $request->qcc_period_id,
                'theme_name' => $request->theme_name,
                'description' => $request->description,
                'status' => 'ACTIVE',
                'created_by' => $user->npk,
            ]);

            DB::commit();
            return redirect()->route('qcc.karyawan.themes', ['circle_id' => $circle->id])
                ->with('success', 'Tema berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: '.$e->getMessage());
        }
    }

    public function updateTheme(Request $request, $id)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();
        if (!$user) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $theme = QccTheme::with('circle')->findOrFail($id);
        $circle = $theme->circle;

        // Pastikan user leader
        $isLeader = QccCircleMember::where('qcc_circle_id', $circle->id)
            ->where('employee_npk', $user->npk)
            ->where('role', 'LEADER')
            ->exists();
        if (!$isLeader) {
            return redirect()->back()->with('error', 'Hanya leader yang bisa mengubah tema.');
        }

        // Cek apakah sudah ada progress step yang sudah diapprove
        $hasApprovedStep = QccCircleStepTransaction::where('qcc_theme_id', $theme->id)
            ->where('status', 'APPROVED')
            ->exists();
        if ($hasApprovedStep) {
            return redirect()->back()->with('error', 'Tidak bisa mengubah tema karena sudah ada progres yang disetujui.');
        }

        $request->validate([
            'theme_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
        ]);

        try {
            $theme->update([
                'theme_name' => $request->theme_name,
                'description' => $request->description,
                'status' => $request->status ?? $theme->status,
            ]);

            return redirect()->route('qcc.karyawan.themes', ['circle_id' => $circle->id])
                ->with('success', 'Tema berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: '.$e->getMessage());
        }
    }

    public function deleteTheme($id)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();
        if (!$user) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $theme = QccTheme::with('circle')->findOrFail($id);
        $circle = $theme->circle;

        // Pastikan user leader
        $isLeader = QccCircleMember::where('qcc_circle_id', $circle->id)
            ->where('employee_npk', $user->npk)
            ->where('role', 'LEADER')
            ->exists();
        if (!$isLeader) {
            return redirect()->back()->with('error', 'Hanya leader yang bisa menghapus tema.');
        }

        // Cek apakah sudah ada file upload (progress) untuk tema ini
        $hasAnyUpload = QccCircleStepTransaction::where('qcc_theme_id', $theme->id)->exists();
        if ($hasAnyUpload) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus tema karena sudah ada progres yang diupload.');
        }

        try {
            DB::beginTransaction();
            $theme->delete();
            DB::commit();

            return redirect()->route('qcc.karyawan.themes', ['circle_id' => $circle->id])
                ->with('success', 'Tema berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: '.$e->getMessage());
        }
    }

    public function progress(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();
        if (!$user) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $themeId = $request->get('theme_id');

        $myCircleIds = QccCircleMember::where('employee_npk', $user->npk)->pluck('qcc_circle_id');
        $myThemes = QccTheme::with('circle')->whereIn('qcc_circle_id', $myCircleIds)->orderBy('created_at', 'desc')->get();

        if (!$themeId) {
            $activeTheme = $myThemes->where('status', 'ACTIVE')->first() ?? $myThemes->first();
            if (!$activeTheme) return redirect()->route('qcc.karyawan.my_circle')->with('warning', 'Buat tema dulu.');
            return redirect()->route('qcc.karyawan.progress', ['theme_id' => $activeTheme->id]);
        }

        $theme = QccTheme::with('circle')->findOrFail($themeId);
        $steps = QccStep::orderBy('step_number', 'asc')->get();
        $uploads = QccCircleStepTransaction::where('qcc_theme_id', $theme->id)->get()->keyBy('qcc_step_id');
        $sevenTools = QccSevenTool::orderBy('tool_name', 'asc')->get();

        $actualSteps = $steps->where('step_number', '>', 0)->values();
        foreach ($actualSteps as $index => $step) {
            if ($index === 0) $step->is_locked = false;
            else {
                $prevUpload = $uploads[$actualSteps[$index - 1]->id] ?? null;
                $step->is_locked = !($prevUpload && $prevUpload->status === 'APPROVED');
            }
        }
        return view('qcc.karyawan.progress', compact('user', 'theme', 'actualSteps', 'uploads', 'myThemes', 'sevenTools'));
    }

    public function uploadFile(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        
        $request->validate([
            'qcc_step_id' => 'required',
            'qcc_theme_id' => 'required',
            'qcc_circle_id' => 'required',
            'file' => 'required|mimes:pdf|max:10240'
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                // Ambil NPK dari Auth user yang login (bukan dari employee, tapi langsung dari user)
                $npk = Auth::user()->npk;

                $folderPath = "qcc/progress/circle_{$request->qcc_circle_id}/theme_{$request->qcc_theme_id}";
                $oldTrans = QccCircleStepTransaction::where([
                    'qcc_circle_id' => $request->qcc_circle_id,
                    'qcc_theme_id' => $request->qcc_theme_id,
                    'qcc_step_id' => $request->qcc_step_id
                ])->first();

                if ($oldTrans && Storage::disk('public')->exists($oldTrans->file_path)) {
                    Storage::disk('public')->delete($oldTrans->file_path);
                }

                $path = $file->storeAs($folderPath, "Step_{$request->qcc_step_id}_".time().'.'.$file->getClientOriginalExtension(), 'public');

                QccCircleStepTransaction::updateOrCreate(
                    [
                        'qcc_circle_id' => $request->qcc_circle_id,
                        'qcc_theme_id' => $request->qcc_theme_id,
                        'qcc_step_id' => $request->qcc_step_id
                    ],
                    [
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => 'pdf',
                        'upload_by' => $npk,
                        'status' => 'WAITING SPV',
                        'upload_at' => now()
                    ]
                );
                return redirect()->back()->with('success', 'File berhasil diunggah!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
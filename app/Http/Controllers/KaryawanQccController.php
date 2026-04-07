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
     * Mengambil profil lengkap dari tabel m_employees berdasarkan user yang login di tabel users.
     */
    private function getAuthUser()
    {
        if (!Auth::check()) return null;

        // Ambil NPK dari Laravel Auth (tabel users)
        $npk = Auth::user()->npk; 

        // Pastikan session auth_npk sinkron dengan Auth::user()
        if (session('auth_npk') !== $npk) {
            session(['auth_npk' => $npk]);
        }

        // Ambil data detail (Dept, Section) dari m_employees
        return Employee::with(['job', 'subSection.section', 'section'])
                ->where('npk', $npk)
                ->first();
    }

    /**
     * Validasi akses: Wajib login dan role harus 'employee'
     */
    private function checkAccess()
    {
        if (!Auth::check() || session('active_role') !== 'employee') {
            return false;
        }
        return true;
    }

    public function dashboard(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getAuthUser();

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
        $user = $this->getAuthUser();

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
        $user = $this->getAuthUser();

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
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getAuthUser();
        $deptCode = $user->getDeptCode();

        $request->validate([
            'circle_name' => 'required|string|max:255',
            'members' => 'required|array|min:1',
            'step0_file' => 'required|mimes:pdf|max:10240',
        ]);

        try {
            DB::transaction(function () use ($request, $user, $deptCode) {
                $circle = QccCircle::create([
                    'circle_code' => 'C-'.strtoupper(bin2hex(random_bytes(3))),
                    'circle_name' => $request->circle_name,
                    'department_code' => $deptCode,
                    'qcc_period_id' => QccPeriod::where('status', 'ACTIVE')->value('id') ?? 1,
                    'status' => 'WAITING SPV',
                ]);

                if ($request->hasFile('step0_file')) {
                    $file = $request->file('step0_file');
                    $path = $file->storeAs("qcc/step0/circle_{$circle->id}", 'Step0_'.time().'.'.$file->getClientOriginalExtension(), 'public');
                    $circle->update(['step0_file_name' => $file->getClientOriginalName(), 'step0_file_path' => $path]);
                }

                // Simpan Leader menggunakan NPK dari Auth
                QccCircleMember::create([
                    'qcc_circle_id' => $circle->id,
                    'employee_npk' => $user->npk,
                    'role' => 'LEADER',
                    'is_active' => 1, 'joined_at' => now(),
                ]);

                foreach ($request->members as $npk) {
                    QccCircleMember::create([
                        'qcc_circle_id' => $circle->id,
                        'employee_npk' => $npk,
                        'role' => 'MEMBER',
                        'is_active' => 1, 'joined_at' => now(),
                    ]);
                }
            });
            return redirect()->back()->with('success', 'Circle & Dokumen Step 0 berhasil didaftarkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: '.$e->getMessage());
        }
    }

    public function themes(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getAuthUser();
        $circleId = $request->get('circle_id');
        $myCircleIds = QccCircleMember::where('employee_npk', $user->npk)->pluck('qcc_circle_id');

        if ($myCircleIds->isEmpty()) return redirect()->route('qcc.karyawan.my_circle')->with('info', 'Buat circle dulu!');
        if (!$circleId) return redirect()->route('qcc.karyawan.themes', ['circle_id' => $myCircleIds->first()]);

        $circle = QccCircle::findOrFail($circleId);
        if ($circle->status !== 'ACTIVE') return redirect()->route('qcc.karyawan.my_circle')->with('warning', 'Circle belum di-approve.');

        $themes = QccTheme::with('period')->where('qcc_circle_id', $circleId)->orderBy('created_at', 'desc')->paginate(10);
        $myCircles = QccCircle::whereIn('id', $myCircleIds)->get();
        $activePeriods = QccPeriod::where('status', 'ACTIVE')->get();

        return view('qcc.karyawan.manage_themes', compact('user', 'circle', 'themes', 'activePeriods', 'myCircles'));
    }

    public function progress(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getAuthUser();
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
        $request->validate(['qcc_step_id' => 'required', 'qcc_theme_id' => 'required', 'qcc_circle_id' => 'required', 'file' => 'required|mimes:pdf|max:10240']);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $npk = Auth::user()->npk; // Ambil NPK dari Auth asli

                $folderPath = "qcc/progress/circle_{$request->qcc_circle_id}/theme_{$request->qcc_theme_id}";
                $oldTrans = QccCircleStepTransaction::where(['qcc_circle_id' => $request->qcc_circle_id, 'qcc_theme_id' => $request->qcc_theme_id, 'qcc_step_id' => $request->qcc_step_id])->first();

                if ($oldTrans && Storage::disk('public')->exists($oldTrans->file_path)) {
                    Storage::disk('public')->delete($oldTrans->file_path);
                }

                $path = $file->storeAs($folderPath, "Step_{$request->qcc_step_id}_".time().'.'.$file->getClientOriginalExtension(), 'public');

                QccCircleStepTransaction::updateOrCreate(
                    ['qcc_circle_id' => $request->qcc_circle_id, 'qcc_theme_id' => $request->qcc_theme_id, 'qcc_step_id' => $request->qcc_step_id],
                    ['file_name' => $file->getClientOriginalName(), 'file_path' => $path, 'file_type' => 'pdf', 'upload_by' => $npk, 'status' => 'WAITING SPV', 'upload_at' => now()]
                );
                return redirect()->back()->with('success', 'File berhasil diunggah!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
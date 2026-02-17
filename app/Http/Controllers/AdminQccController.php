<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\QccCircle;
use App\Models\QccStep;
use App\Models\QccPeriod;
use App\Models\QccPeriodStep;
use App\Models\QccTarget;
use App\Models\Department;
use App\Models\Division;
use App\Models\QccCircleStepTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminQccController extends Controller
{
    public function index(Request $request)
    {
        $user = Employee::with(['subSection.section.department.division', 'section.department.division'])->find(Auth::id());
        
        $viewLevel = $request->get('view_level', 'company'); 
        $selectedPeriod = $request->get('period_id');
        $selectedDiv = $request->get('division_code');
        $isAdmin = (session('active_role') === 'admin');

        $periods = QccPeriod::orderBy('year', 'desc')->get();
        if (!$selectedPeriod) {
            $selectedPeriod = QccPeriod::where('status', 'ACTIVE')->value('id') ?? QccPeriod::orderBy('id', 'desc')->value('id');
        }

        // --- LOGIKA PROGRESS LINE (DISKRIT / MELOMPAT) ---
        $today = Carbon::now();
        $todayDate = $today->format('d/m/Y'); // <--- TAMBAHKAN BARIS INI UNTUK MEMPERBAIKI ERROR
        
        $progressLineX = 0; 
        $period = QccPeriod::find($selectedPeriod);

        $periodSteps = QccPeriodStep::where('qcc_period_id', $selectedPeriod)
            ->join('m_qcc_steps', 'm_qcc_period_steps.qcc_step_id', '=', 'm_qcc_steps.id')
            ->orderBy('m_qcc_steps.step_number', 'asc')
            ->get(['m_qcc_period_steps.deadline_date', 'm_qcc_steps.step_number', 'm_qcc_steps.id as step_id']);

        if ($period && $periodSteps->count() > 0) {
            foreach ($periodSteps as $index => $ps) {
                $deadline = Carbon::parse($ps->deadline_date);
                if ($today->lte($deadline)) {
                    $progressLineX = $index; 
                    break;
                }
                $progressLineX = $index;
            }
        }
        
        // --- GENERATE LABEL BULAN PER STEP ---
        $stepMonths = [];
        if ($period) {
            $stepMonths[] = Carbon::parse($period->start_date)->translatedFormat('M');
            foreach ($periodSteps as $ps) {
                $stepMonths[] = Carbon::parse($ps->deadline_date)->translatedFormat('M');
            }
        }
        while(count($stepMonths) < 9) { $stepMonths[] = ''; }

        // Tentukan Otoritas Data & Filter
        $selectedDept = null;
        if ($isAdmin) {
            $selectedDept = $request->get('department_code');
        } elseif ($user->occupation === 'GMR') {
            if ($viewLevel === 'company') $viewLevel = 'division';
            $myDept = Department::where('code', $user->getDeptCode())->first();
            $selectedDiv = $myDept ? $myDept->code_division : null;
        } elseif (in_array($user->occupation, ['KDP', 'SPV'])) {
            if (!in_array($viewLevel, ['department', 'circle'])) $viewLevel = 'department';
            $selectedDept = $user->getDeptCode();
            $deptData = Department::where('code', $selectedDept)->first();
            $selectedDiv = $deptData ? $deptData->code_division : null;
        }

        $stats = $this->calculateStats($selectedPeriod, $viewLevel, $selectedDiv, $selectedDept, $progressLineX, $periodSteps);

        // LOGIKA MULTI-CHART
        $charts = [];
        if ($viewLevel == 'company' && $isAdmin) {
            $charts[] = ['title' => 'All Company Overview', 'data' => $this->getChartData($selectedPeriod)];
        } elseif ($viewLevel == 'division') {
            $divQuery = Division::query();
            if (!$isAdmin) $divQuery->where('code', $selectedDiv);
            foreach ($divQuery->get() as $div) {
                $deptCodes = Department::where('code_division', $div->code)->pluck('code');
                $charts[] = ['title' => 'Division: ' . $div->name, 'data' => $this->getChartData($selectedPeriod, $deptCodes)];
            }
        } elseif ($viewLevel == 'department') {
            $deptQuery = Department::query();
            if (!$isAdmin && in_array($user->occupation, ['KDP', 'SPV'])) {
                $deptQuery->where('code', $selectedDept);
            } elseif (!$isAdmin && $user->occupation === 'GMR') {
                $deptQuery->where('code_division', $selectedDiv);
            } else {
                if ($selectedDiv) $deptQuery->where('code_division', $selectedDiv);
            }
            foreach ($deptQuery->orderBy('name', 'asc')->get() as $dept) {
                $charts[] = ['title' => 'Dept: ' . $dept->name, 'data' => $this->getChartData($selectedPeriod, [$dept->code])];
            }
        } elseif ($viewLevel == 'circle') {
            $circles = QccCircle::where('department_code', $selectedDept)
                        ->where('qcc_period_id', $selectedPeriod)
                        ->orderBy('circle_name', 'asc')->get();
            foreach ($circles as $circle) {
                $charts[] = ['title' => 'Circle: ' . $circle->circle_name, 'data' => $this->getChartDataPerCircle($circle->id)];
            }
        }

        $divisions = Division::all();
        return view('qcc.admin.dashboard', compact(
            'user', 'stats', 'periods', 'divisions', 
            'selectedPeriod', 'viewLevel', 'selectedDiv', 'selectedDept', 'charts', 'progressLineX', 'todayDate', 'stepMonths'
        ));
    }
    /**
     * Helper khusus untuk mengambil data 1 Circle saja
     */
    private function getChartDataPerCircle($circleId)
    {
        $submitted = []; $approved = [];
        
        // Step 0 (Registration)
        $circle = QccCircle::find($circleId);
        $submitted[] = 1; // Karena circle ini sudah ada
        $approved[] = ($circle->status === 'ACTIVE') ? 1 : 0;

        // Step 1 - 8
        for ($i = 1; $i <= 8; $i++) {
            $trans = QccCircleStepTransaction::where('qcc_circle_id', $circleId)
                        ->where('qcc_step_id', $i)->first();
            
            $submitted[] = ($trans) ? 1 : 0;
            $approved[] = ($trans && $trans->status === 'APPROVED') ? 1 : 0;
        }

        return [
            'submitted' => $submitted,
            'approved' => $approved,
            'target' => array_fill(0, 9, 1) // Target per circle selalu 1 project
        ];
    }

    private function getChartData($periodId, $deptCodes = null)
    {
        $submitted = []; $approved = [];
        $targetValue = QccTarget::where('qcc_period_id', $periodId)->when($deptCodes, fn($q) => $q->whereIn('department_code', $deptCodes))->sum('target_amount');
        $step0Query = QccCircle::where('qcc_period_id', $periodId)->when($deptCodes, fn($q) => $q->whereIn('department_code', $deptCodes));
        $submitted[] = (clone $step0Query)->count();
        $approved[] = (clone $step0Query)->where('status', 'ACTIVE')->count();
        for ($i = 1; $i <= 8; $i++) {
            $baseQuery = QccCircleStepTransaction::where('qcc_step_id', $i)->whereHas('circle', function($q) use ($periodId, $deptCodes) {
                $q->where('qcc_period_id', $periodId);
                if ($deptCodes) $q->whereIn('department_code', $deptCodes);
            });
            $submitted[] = (clone $baseQuery)->count();
            $approved[] = (clone $baseQuery)->where('status', 'APPROVED')->count();
        }
        return ['submitted' => $submitted, 'approved' => $approved, 'target' => array_fill(0, 9, (int)$targetValue)];
    }

    private function calculateStats($periodId, $level, $divCode, $deptCode, $progressLineX, $periodSteps)
    {
        // Tentukan jangkauan Departemen berdasarkan filter
        $deptCodes = $deptCode ? [$deptCode] : null;
        if(!$deptCodes && $divCode) { 
            $deptCodes = Department::where('code_division', $divCode)->pluck('code'); 
        }

        // 1. Target dan Total Circle (Step 0)
        $target = QccTarget::where('qcc_period_id', $periodId)
            ->when($deptCodes, fn($q) => $q->whereIn('department_code', $deptCodes))
            ->sum('target_amount');

        $actual = QccCircle::where('qcc_period_id', $periodId)
            ->when($deptCodes, fn($q) => $q->whereIn('department_code', $deptCodes))
            ->count();

        // 2. LOGIKA NEED REVIEW (Step 0 + Step 1-8)
        
        // Hitung Pending dari Step 0 (Registrasi Circle)
        $needReviewStep0 = QccCircle::where('qcc_period_id', $periodId)
            ->whereIn('status', ['WAITING SPV', 'WAITING KDP'])
            ->when($deptCodes, fn($q) => $q->whereIn('department_code', $deptCodes))
            ->count();

        // Hitung Pending dari Step 1-8 (Progres PDCA)
        $needReviewSteps = QccCircleStepTransaction::whereIn('status', ['WAITING SPV', 'WAITING KDP'])
            ->whereHas('circle', function($q) use ($periodId, $deptCodes) {
                $q->where('qcc_period_id', $periodId);
                if ($deptCodes) $q->whereIn('department_code', $deptCodes);
            })->count();

        // Total gabungan
        $totalNeedReview = $needReviewStep0 + $needReviewSteps;

        // 3. Logika Circle Selesai berdasarkan Garis Merah
        $completedCount = 0;
        $targetStepNumber = floor($progressLineX ?? 0); 

        if ($targetStepNumber == 0) {
            $completedCount = QccCircle::where('qcc_period_id', $periodId)
                ->where('status', 'ACTIVE')
                ->when($deptCodes, fn($q) => $q->whereIn('department_code', $deptCodes))
                ->count();
        } else {
            $targetStep = $periodSteps->where('step_number', $targetStepNumber)->first();
            if ($targetStep) {
                $completedCount = QccCircleStepTransaction::where('qcc_step_id', $targetStep->step_id)
                    ->where('status', 'APPROVED') 
                    ->whereHas('circle', function($q) use ($periodId, $deptCodes) {
                        $q->where('qcc_period_id', $periodId);
                        if ($deptCodes) $q->whereIn('department_code', $deptCodes);
                    })->count();
            }
        }

        return [
            'total_circles' => $actual,
            'target_circles' => $target,
            'need_review'   => $totalNeedReview, // Sekarang sudah termasuk Step 0
            'completed'     => $completedCount,
        ];
    }

    // Master Steps QCC
    public function masterSteps(Request $request)
    {
        $user = Employee::with('job')->find(Auth::id());
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10); 

        $steps = QccStep::when($search, function($query) use ($search) {
                $query->where('step_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('step_number', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        return view('qcc.admin.master_qcc_steps', compact('user', 'steps', 'perPage'));
    }

    public function storeStep(Request $request)
    {
        $request->validate([
            'step_number' => 'required|numeric|unique:m_qcc_steps,step_number',
            'step_name' => 'required',
            'template_file' => 'nullable|mimes:ppt,pptx,xls,xlsx,pdf|max:10240',
        ]);

        $data = $request->only(['step_number', 'step_name', 'description']);

        if ($request->hasFile('template_file')) {
            $file = $request->file('template_file');
            $fileName = 'Master_Template_Step_' . $request->step_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('qcc/templates', $fileName, 'public');

            $data['template_file_name'] = $file->getClientOriginalName();
            $data['template_file_path'] = $path;
        }

        QccStep::create($data);
        return redirect()->back()->with('success', 'Step baru dan template berhasil ditambahkan!');
    }

    public function updateStep(Request $request, $id)
    {
        $step = QccStep::findOrFail($id);

        $request->validate([
            'step_name' => 'required',
            'template_file' => 'nullable|mimes:ppt,pptx,xls,xlsx,pdf|max:10240',
        ]);

        $data = $request->only(['step_name', 'description']);

        if ($request->hasFile('template_file')) {
            // Hapus file lama jika ada
            if ($step->template_file_path && Storage::disk('public')->exists($step->template_file_path)) {
                Storage::disk('public')->delete($step->template_file_path);
            }

            $file = $request->file('template_file');
            $fileName = 'Master_Template_Step_' . $step->step_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('qcc/templates', $fileName, 'public');

            $data['template_file_name'] = $file->getClientOriginalName();
            $data['template_file_path'] = $path;
        }

        $step->update($data);
        return redirect()->back()->with('success', 'Data Step dan Template berhasil diperbarui!');
    }

    public function deleteStep($id)
    {
        $step = QccStep::findOrFail($id);
        // Hapus file fisik jika ada
        if ($step->template_file_path && Storage::disk('public')->exists($step->template_file_path)) {
            Storage::disk('public')->delete($step->template_file_path);
        }
        $step->delete();
        return redirect()->back()->with('success', 'Step berhasil dihapus!');
    }

    // Master Periods QCC
    public function masterPeriods(Request $request)
    {
        $user = Employee::with('job')->find(Auth::id());
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $periods = QccPeriod::with('periodSteps.step')
            ->when($search, function($query) use ($search) {
                $query->where('period_name', 'like', "%{$search}%")
                      ->orWhere('period_code', 'like', "%{$search}%")
                      ->orWhere('year', 'like', "%{$search}%");
            })
            ->orderBy('year', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Ambil master steps untuk keperluan modal setting deadline
        $masterSteps = QccStep::orderBy('step_number', 'asc')->get();

        return view('qcc.admin.master_qcc_periods', compact('user', 'periods', 'perPage', 'masterSteps'));
    }

    public function storePeriod(Request $request)
    {
        $request->validate([
            'period_code' => 'required|unique:m_qcc_periods,period_code',
            'period_name' => 'required|string|max:255',
            'year'        => 'required|digits:4',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        $newPeriod = DB::transaction(function () use ($request) {
            // Status otomatis ACTIVE saat pertama kali simpan
            $data = $request->all();
            $data['status'] = 'ACTIVE';
            
            $period = QccPeriod::create($data);

            $steps = QccStep::orderBy('step_number', 'asc')->get();
            foreach ($steps as $step) {
                QccPeriodStep::create([
                    'qcc_period_id' => $period->id,
                    'qcc_step_id'   => $step->id,
                    'deadline_date' => $request->end_date
                ]);
            }
            return $period->load('periodSteps.step');
        });

        return redirect()->back()->with([
            'success' => 'Periode ACTIVE berhasil dibuat! Silahkan atur deadline langkah.',
            'auto_open_deadline' => $newPeriod
        ]);
    }

    public function updatePeriod(Request $request, $id)
    {
        $period = QccPeriod::findOrFail($id);

        // LOGIKA 1: Jika yang dikirim adalah form DEADLINE
        if ($request->has('deadlines')) {
            foreach ($request->deadlines as $stepId => $date) {
                // Pastikan update berdasarkan qcc_period_id dan qcc_step_id
                QccPeriodStep::where('qcc_period_id', $id)
                    ->where('qcc_step_id', $stepId)
                    ->update(['deadline_date' => $date]);
            }
            return redirect()->back()->with('success', 'Batas waktu (deadline) langkah berhasil diperbarui!');
        }

        // LOGIKA 2: Jika yang dikirim adalah form EDIT PROFIL PERIODE
        $request->validate([
            'period_code' => 'required|unique:m_qcc_periods,period_code,'.$id,
            'period_name' => 'required',
            'year'        => 'required|digits:4',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        $period->update($request->only(['period_code', 'period_name', 'year', 'start_date', 'end_date', 'status']));

        return redirect()->back()->with('success', 'Data periode berhasil diperbarui!');
    }

    public function deletePeriod($id)
    {
        // Karena ada Cascade di database, m_qcc_period_steps akan otomatis terhapus
        QccPeriod::destroy($id);
        return redirect()->back()->with('success', 'Periode berhasil dihapus!');
    }

    // Master Targets QCC
    public function masterTargets(Request $request)
    {
        $user = Employee::with('job')->where('npk', session('auth_npk'))->first();
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $targets = QccTarget::with(['period', 'department'])
            ->whereHas('department', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('period', function($q) use ($search) {
                $q->where('period_name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)->withQueryString();

        $periods = QccPeriod::where('status', 'ACTIVE')->get();
        $departments = Department::orderBy('name', 'asc')->get();

        return view('qcc.admin.master_qcc_targets', compact('user', 'targets', 'perPage', 'periods', 'departments'));
    }

    public function storeTarget(Request $request)
    {
        $request->validate([
            'qcc_period_id' => 'required',
            'department_code' => 'required',
            'target_amount' => 'required|numeric|min:1'
        ]);

        // Cek duplikasi
        $exists = QccTarget::where('qcc_period_id', $request->qcc_period_id)
                        ->where('department_code', $request->department_code)
                        ->exists();
        
        if($exists) return redirect()->back()->with('error', 'Target untuk Departemen ini di periode tersebut sudah ada!');

        QccTarget::create($request->all());
        return redirect()->back()->with('success', 'Target berhasil ditetapkan!');
    }

    public function updateTarget(Request $request, $id)
    {
        $target = QccTarget::findOrFail($id);
        $target->update($request->all());
        return redirect()->back()->with('success', 'Target berhasil diperbarui!');
    }

    public function deleteTarget($id)
    {
        QccTarget::destroy($id);
        return redirect()->back()->with('success', 'Target berhasil dihapus!');
    }

    // MANAGE KARYAWAN
    // Master Employees (Karyawan)
    public function masterEmployees(Request $request)
    {
        $user = Employee::with('job')->where('npk', session('auth_npk'))->first();
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $employees = Employee::with(['job', 'subSection.section.department'])
            ->when($search, function($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('npk', 'like', "%{$search}%");
            })
            ->orderBy('nama', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        // Data untuk Dropdown di Modal
        $occupations = \App\Models\Occupation::all();
        $subSections = \App\Models\SubSection::with('section.department')->get();

        return view('qcc.admin.master_qcc_employees', compact('user', 'employees', 'perPage', 'occupations', 'subSections'));
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'npk' => 'required|unique:m_employees,npk',
            'nama' => 'required|string|max:255',
            'line_code' => 'required',
            'sub_section' => 'required',
            'occupation' => 'required',
        ]);

        Employee::create($request->all());
        return redirect()->back()->with('success', 'Karyawan baru berhasil ditambahkan!');
    }

    public function updateEmployee(Request $request, $id)
    {
        $emp = Employee::findOrFail($id);
        $request->validate([
            'npk' => 'required|unique:m_employees,npk,' . $id,
            'nama' => 'required',
        ]);

        $emp->update($request->all());
        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function deleteEmployee($id)
    {
        Employee::destroy($id);
        return redirect()->back()->with('success', 'Data karyawan telah dihapus.');
    }
}   
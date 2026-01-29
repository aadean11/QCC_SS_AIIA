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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminQccController extends Controller
{
    public function index(Request $request)
    {
        $user = Employee::with('job')->find(Auth::id());
        
        $selectedDept = $request->get('department_code');
        $selectedPeriod = $request->get('period_id');

        $departments = Department::orderBy('name', 'asc')->get();
        $periods = QccPeriod::orderBy('year', 'desc')->orderBy('period_name', 'asc')->get();

        if (!$selectedPeriod) {
            $activePeriod = QccPeriod::where('status', 'ACTIVE')->first() ?? QccPeriod::orderBy('id', 'desc')->first();
            $selectedPeriod = $activePeriod ? $activePeriod->id : null;
        }

        // 1. Ambil Target
        $targetValue = QccTarget::where('qcc_period_id', $selectedPeriod)
            ->when($selectedDept, fn($q) => $q->where('department_code', $selectedDept))
            ->sum('target_amount');

        // 2. Hitung Stats untuk Mini Cards
        $actualCircles = QccCircle::where('qcc_period_id', $selectedPeriod)
            ->when($selectedDept, fn($q) => $q->where('department_code', $selectedDept))
            ->count();

        $stats = [
            'total_circles' => $actualCircles,
            'target_circles' => $targetValue,
            'need_review' => \App\Models\QccCircleStepTransaction::whereIn('status', ['WAITING SPV', 'WAITING KADEPT', 'WAITING KDP'])
                ->whereHas('circle', function($q) use ($selectedPeriod, $selectedDept) {
                    $q->where('qcc_period_id', $selectedPeriod);
                    if ($selectedDept) $q->where('department_code', $selectedDept);
                })->count(),
            'completed' => \App\Models\QccCircleStepTransaction::where('qcc_step_id', 8)
                ->where('status', 'APPROVED')
                ->whereHas('circle', function($q) use ($selectedPeriod, $selectedDept) {
                    $q->where('qcc_period_id', $selectedPeriod);
                    if ($selectedDept) $q->where('department_code', $selectedDept);
                })->count(),
            'active_periods' => QccPeriod::where('status', 'ACTIVE')->count()
        ];

        // 3. Data Chart (Step 0 s/d Step 8)
        $chartSubmitted = [];
        $chartApproved = [];

        // --- LOGIKA STEP 0 (Registrasi Circle) ---
        // Submitted Step 0 = Semua yang mendaftar (Waiting + Active)
        $step0Submitted = QccCircle::where('qcc_period_id', $selectedPeriod)
            ->whereIn('status', ['WAITING SPV', 'WAITING KDP', 'ACTIVE']) // Sertakan ACTIVE
            ->when($selectedDept, fn($q) => $q->where('department_code', $selectedDept))
            ->count();
        
        // Approved Step 0 = Hanya yang sudah ACTIVE
        $step0Approved = QccCircle::where('qcc_period_id', $selectedPeriod)
            ->where('status', 'ACTIVE')
            ->when($selectedDept, fn($q) => $q->where('department_code', $selectedDept))
            ->count();

        $chartSubmitted = [$step0Submitted];
        $chartApproved = [$step0Approved];

        // --- LOGIKA STEP 1 - 8 (Progres Dokumen) ---
        for ($i = 1; $i <= 8; $i++) {
            $baseQuery = \App\Models\QccCircleStepTransaction::where('qcc_step_id', $i)
                ->whereHas('circle', function($q) use ($selectedPeriod, $selectedDept) {
                    $q->where('qcc_period_id', $selectedPeriod);
                    if ($selectedDept) $q->where('department_code', $selectedDept);
                });

            // Submitted = Semua yang upload (Waiting + Approved)
            $totalSubmitted = (clone $baseQuery)->whereIn('status', ['WAITING SPV', 'WAITING KADEPT', 'WAITING KDP', 'APPROVED'])->count();
            // Approved = Hanya yang statusnya APPROVED
            $totalApproved = (clone $baseQuery)->where('status', 'APPROVED')->count();

            $chartSubmitted[] = $totalSubmitted;
            $chartApproved[] = $totalApproved;
        }

        $chartData = [
            'submitted' => $chartSubmitted,
            'approved' => $chartApproved,
            'target' => array_fill(0, 9, (int)$targetValue)
        ];

        return view('qcc.admin.dashboard', compact('user', 'stats', 'departments', 'periods', 'selectedDept', 'selectedPeriod', 'chartData'));
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
        $request->validate([
            'period_code' => 'required|unique:m_qcc_periods,period_code,'.$id,
            'period_name' => 'required',
            'year'        => 'required|digits:4',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        $period = QccPeriod::findOrFail($id);
        $period->update($request->all());

        // Update deadlines jika ada data dikirim dari modal deadline
        if ($request->has('deadlines')) {
            foreach ($request->deadlines as $stepId => $date) {
                QccPeriodStep::where('qcc_period_id', $id)
                    ->where('qcc_step_id', $stepId)
                    ->update(['deadline_date' => $date]);
            }
        }

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
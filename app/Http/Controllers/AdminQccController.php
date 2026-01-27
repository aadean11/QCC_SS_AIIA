<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\QccCircle;
use App\Models\QccStep;
use App\Models\QccPeriod;
use App\Models\QccPeriodStep;
use Illuminate\Support\Facades\DB;

class AdminQccController extends Controller
{
    public function index(Request $request)
    {
        $user = Employee::with('job')->find(Auth::id());
        
        // Ambil semua departemen untuk dropdown filter
        $departments = \App\Models\Department::orderBy('name', 'asc')->get();

        // Logika Filter (Contoh sederhana)
        $selectedDept = $request->get('department_code');
        
        // Di sini Anda biasanya melakukan query ke database berdasarkan $selectedDept
        // Untuk contoh ini, kita asumsikan data chart berubah jika filter dipilih
        $stats = [
            'total_circles' => $selectedDept ? 15 : 124, // Contoh angka berubah
            'need_review' => 15,
            'completed' => 45,
            'active_periods' => 2
        ];

        // Data Chart (Nanti diisi dari query database berdasarkan $selectedDept)
        $chartData = [
            'submitted' => $selectedDept ? [2, 4, 3, 5, 2, 6, 8, 5, 4] : [6, 8, 7, 10, 4, 9, 10, 12, 11],
            'approved' => $selectedDept ? [1, 2, 2, 4, 1, 5, 3, 2, 1] : [5, 5, 4, 9, 3, 10, 6, 5, 4]
        ];

        return view('qcc.admin.dashboard', compact('user', 'stats', 'departments', 'selectedDept', 'chartData'));
    }

    // Master Steps QCC
    public function masterSteps(Request $request)
    {
        $user = Employee::with('job')->find(Auth::id());
        
        // Ambil input search dan per_page (default 10)
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10); 

        $steps = QccStep::when($search, function($query) use ($search) {
                $query->where('step_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('step_number', 'asc')
            ->paginate($perPage) // Gunakan paginate, bukan get()
            ->withQueryString(); // Menjaga parameter search & per_page saat pindah halaman

        return view('qcc.admin.master_qcc_steps', compact('user', 'steps', 'perPage'));
    }

    public function storeStep(Request $request)
    {
        $request->validate([
            'step_number' => 'required|numeric|unique:m_qcc_steps,step_number',
            'step_name' => 'required',
        ]);

        QccStep::create($request->all());
        return redirect()->back()->with('success', 'Step berhasil ditambahkan!');
    }

    public function updateStep(Request $request, $id)
    {
        $step = QccStep::findOrFail($id);
        $step->update($request->all());
        return redirect()->back()->with('success', 'Step berhasil diperbarui!');
    }

    public function deleteStep($id)
    {
        QccStep::destroy($id);
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
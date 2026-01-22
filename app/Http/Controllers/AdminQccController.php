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
    public function index()
    {
        $user = Employee::with('job')->find(Auth::id());
        
        // Tambahkan ini agar layout 'welcome' tidak error
        $jumlahQcc = QccCircle::count(); 
        $jumlahSs = 100; // Sesuaikan dengan logika Anda

        $stats = [
            'total_circles' => $jumlahQcc,
            'active_periods' => 2,
            'need_review' => 15,
            'completed' => 45
        ];

        $circles = []; 

        // Kirimkan jumlahQcc dan jumlahSs ke view
        return view('qcc.admin.dashboard', compact('user', 'stats', 'circles', 'jumlahQcc', 'jumlahSs'));
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
}   
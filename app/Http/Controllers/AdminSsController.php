<?php

namespace App\Http\Controllers;

use App\Models\SsSubmission;
use App\Models\Department;
use App\Models\SsScoringRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSsController extends Controller
{
    private function checkAdmin()
    {
        return Auth::check() && session('active_role') === 'admin';
    }

    private function getUser()
    {
        $user = Auth::user();
        return $user ? $user->employee : null;
    }

    public function dashboard(Request $request)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        // Ambil filter
        $selectedMonth = (int) $request->get('month', date('m'));
        $selectedYear = (int) $request->get('year', date('Y'));
        $selectedDept = $request->get('department_code'); // bisa null

        // Query dasar dengan filter tahun, bulan, dan departemen (opsional)
        $query = SsSubmission::whereYear('submission_date', $selectedYear)
                            ->whereMonth('submission_date', $selectedMonth);

        if ($selectedDept) {
            $query->where('department_code', $selectedDept);
        }

        // Statistik card
        $total = (clone $query)->count();
        $pendingSpv = (clone $query)->where('status', 'spv_review')->count();
        $approved = (clone $query)->where('status', 'approved')->count();
        $rewarded = (clone $query)->where('status', 'rewarded')->count();

        // Data grafik batang: perkembangan SS per bulan pada tahun dan departemen terfilter
        $monthlyQuery = SsSubmission::whereYear('submission_date', $selectedYear);
        if ($selectedDept) {
            $monthlyQuery->where('department_code', $selectedDept);
        }
        $monthlyData = $monthlyQuery->selectRaw('DATE_FORMAT(submission_date, "%b") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderByRaw('MIN(submission_date)')
            ->get();

        // Data grafik pie: distribusi status berdasarkan semua filter
        $statusData = (clone $query)->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Mapping status label
        $statusLabels = [
            'submitted'   => 'Submitted',
            'assessed'    => 'Assessed',
            'spv_review'  => 'SPV Review',
            'kdp_review'  => 'KDP Review',
            'approved'    => 'Approved',
            'rejected'    => 'Rejected',
            'rewarded'    => 'Rewarded'
        ];
        foreach ($statusData as $item) {
            $item->status_label = $statusLabels[$item->status] ?? $item->status;
        }

        // Data untuk dropdown
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $years = range(date('Y') - 5, date('Y'));
        $departments = Department::orderBy('name')->get(); // pastikan kolom 'code' dan 'name' ada

        return view('ss.admin.dashboard', compact(
            'user', 'total', 'pendingSpv', 'approved', 'rewarded',
            'monthlyData', 'statusData', 'selectedMonth', 'selectedYear', 'selectedDept',
            'months', 'years', 'departments'
        ));
    }

    public function submissions(Request $request)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');
        $status = $request->get('status');

        $query = SsSubmission::with(['employee', 'ldr', 'spv', 'kdp']);

        if ($status) {
            $query->where('status', $status);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('employee', function($sq) use ($search) {
                    $sq->where('nama', 'like', "%{$search}%");
                })->orWhere('department_code', 'like', "%{$search}%");
            });
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('ss.admin.submissions', compact('user', 'submissions'));
    }

    public function show($id)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $submission = SsSubmission::with(['employee', 'ldr', 'spv', 'kdp'])->findOrFail($id);

        return view('ss.admin.show', compact('user', 'submission'));
    }

    public function rewardForm($id)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $submission = SsSubmission::findOrFail($id);
        if ($submission->status === 'rewarded') {
            return redirect()->route('ss.admin.show', $submission->id)->with('info', 'Reward untuk SS ini sudah dibayar.');
        }
        if ($submission->status !== 'approved') {
            return redirect()->route('ss.admin.show', $submission->id)->with('error', 'Reward hanya bisa dikonfirmasi untuk SS yang sudah approved.');
        }

        if ($submission->calculated_reward_amount === null) {
            $submission->calculated_reward_amount = SsScoringRange::rewardForScore($submission->final_score ?? $submission->score);
        }
        if ($submission->reward_amount === null) {
            $submission->reward_amount = $submission->calculated_reward_amount;
        }

        return view('ss.admin.reward', compact('user', 'submission'));
    }

    public function rewardStore(Request $request, $id)
    {
        if (!$this->checkAdmin()) abort(403);

        $request->validate([
            'reward_amount' => 'required|numeric|min:0',
        ]);

        $submission = SsSubmission::findOrFail($id);
        if ($submission->status === 'rewarded') {
            return redirect()->route('ss.admin.show', $submission->id)->with('info', 'Reward untuk SS ini sudah pernah dibayar.');
        }
        if ($submission->status !== 'approved') {
            return redirect()->route('ss.admin.show', $submission->id)->with('error', 'Reward hanya bisa dibayar untuk SS yang sudah approved.');
        }

        $submission->reward_amount = $request->reward_amount;
        $submission->paid_at = now();
        $submission->status = 'rewarded';
        $submission->save();

        return redirect()->route('ss.admin.submissions')->with('success', 'Reward berhasil diberikan.');
    }

    public function masterScoring(Request $request)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');

        $query = SsScoringRange::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('approver_level', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ranking', $search);
            });
        }

        $ranges = $query->orderBy('min_score')->paginate($perPage)->withQueryString();

        return view('ss.admin.master_scoring', compact('user', 'ranges', 'perPage'));
    }

    public function storeScoring(Request $request)
    {
        if (!$this->checkAdmin()) abort(403);

        $data = $this->validateScoringRange($request);
        if ($this->hasOverlappingActiveRange($data)) {
            return redirect()->back()->withInput()->with('error', 'Range total nilai aktif tidak boleh tumpang tindih.');
        }

        SsScoringRange::create($data);

        return redirect()->route('ss.admin.master_scoring')->with('success', 'Master scoring SS berhasil ditambahkan.');
    }

    public function updateScoring(Request $request, $id)
    {
        if (!$this->checkAdmin()) abort(403);

        $range = SsScoringRange::findOrFail($id);
        $data = $this->validateScoringRange($request);
        if ($this->hasOverlappingActiveRange($data, (int) $id)) {
            return redirect()->back()->withInput()->with('error', 'Range total nilai aktif tidak boleh tumpang tindih.');
        }

        $range->update($data);

        return redirect()->route('ss.admin.master_scoring')->with('success', 'Master scoring SS berhasil diperbarui.');
    }

    public function deleteScoring($id)
    {
        if (!$this->checkAdmin()) abort(403);

        SsScoringRange::findOrFail($id)->delete();

        return redirect()->route('ss.admin.master_scoring')->with('success', 'Master scoring SS berhasil dihapus.');
    }

    private function validateScoringRange(Request $request): array
    {
        $data = $request->validate([
            'min_score' => 'required|integer|min:0',
            'max_score' => 'required|integer|gte:min_score',
            'ranking' => 'required|integer|min:1|max:255',
            'reward_amount' => 'required|numeric|min:0',
            'approver_level' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'extra_score_increment' => 'nullable|integer|min:1',
            'extra_reward_increment' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function hasOverlappingActiveRange(array $data, ?int $ignoreId = null): bool
    {
        if (!($data['is_active'] ?? false)) {
            return false;
        }

        return SsScoringRange::where('is_active', true)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('min_score', '<=', $data['max_score'])
            ->where('max_score', '>=', $data['min_score'])
            ->exists();
    }
}

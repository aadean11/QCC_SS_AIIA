<?php

namespace App\Http\Controllers;

use App\Models\SsSubmission;
use App\Models\Department;
use App\Models\Division;
use App\Models\SsTarget;
use App\Models\SsScoringRange;
use App\Services\SsScoringService;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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

    private function getCurrentEmployee()
    {
        $user = Auth::user();

        return $user ? $user->employee : null;
    }

    private function monthNames(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    public function dashboard(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $employee = $this->getCurrentEmployee();
        if (!$employee) {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $user = $employee;
        $isAdmin = session('active_role') === 'admin';
        $canMonitor = $isAdmin || in_array($user->occupation, ['GMR', 'SPV', 'KDP'], true);

        if (!$canMonitor) {
            abort(403, 'Anda tidak memiliki akses monitoring SS.');
        }

        $viewLevel = $request->get('view_level', 'company');
        $selectedYear = (int) $request->get('year', date('Y'));
        $selectedMonth = $request->filled('month') ? (int) $request->get('month') : null;
        $selectedDiv = $request->get('division_code');
        $selectedDept = null;

        $today = Carbon::now();
        $todayDate = $today->format('d/m/Y');

        if ($isAdmin) {
            $selectedDept = $request->get('department_code');
        } elseif ($user->occupation === 'GMR') {
            if ($viewLevel === 'company') {
                $viewLevel = 'division';
            }
            $myDept = Department::where('code', $user->getDeptCode())->first();
            $selectedDiv = $myDept ? $myDept->code_division : null;
        } elseif (in_array($user->occupation, ['KDP', 'SPV'], true)) {
            $viewLevel = 'department';
            $selectedDept = $user->getDeptCode();
            $deptData = Department::where('code', $selectedDept)->first();
            $selectedDiv = $deptData ? $deptData->code_division : null;
        }

        $stats = $this->calculateSsStats($selectedYear, $selectedMonth, $viewLevel, $selectedDiv, $selectedDept);

        $charts = [];
        if ($viewLevel === 'company' && $isAdmin) {
            $charts[] = [
                'title' => 'All Company Overview',
                'data' => $this->getSsChartData($selectedYear, $selectedMonth),
            ];
        } elseif ($viewLevel === 'division') {
            $divQuery = Division::query();
            if (!$isAdmin) {
                $divQuery->where('code', $selectedDiv);
            }
            foreach ($divQuery->orderBy('name')->get() as $div) {
                $deptCodes = Department::where('code_division', $div->code)->pluck('code');
                $charts[] = [
                    'title' => 'Division: '.$div->name,
                    'data' => $this->getSsChartData($selectedYear, $selectedMonth, $deptCodes),
                ];
            }
        } elseif ($viewLevel === 'department') {
            $deptQuery = Department::query();
            if (!$isAdmin && in_array($user->occupation, ['KDP', 'SPV'], true)) {
                $deptQuery->where('code', $selectedDept);
            } elseif (!$isAdmin && $user->occupation === 'GMR') {
                $deptQuery->where('code_division', $selectedDiv);
            } elseif ($selectedDiv) {
                $deptQuery->where('code_division', $selectedDiv);
            }
            foreach ($deptQuery->orderBy('name')->get() as $dept) {
                $charts[] = [
                    'title' => 'Dept: '.$dept->name,
                    'data' => $this->getSsChartData($selectedYear, $selectedMonth, [$dept->code]),
                ];
            }
        }

        $divisions = Division::orderBy('name')->get();
        $months = $this->monthNames();
        $years = range(date('Y') - 2, date('Y') + 1);

        return view('ss.admin.dashboard', compact(
            'user', 'stats', 'divisions', 'selectedYear', 'selectedMonth',
            'viewLevel', 'selectedDiv', 'selectedDept', 'charts', 'todayDate', 'months', 'years', 'isAdmin'
        ));
    }

    private function getSsChartData(int $year, ?int $month = null, $deptCodes = null): array
    {
        if ($month === null) {
            $months = $this->monthNames();
            $labels = array_values($months);
            $target = [];
            $submitted = [];
            $approved = [];

            foreach (array_keys($months) as $monthNumber) {
                $target[] = (int) SsTarget::where('year', $year)
                    ->where('month', $monthNumber)
                    ->when($deptCodes, fn ($q) => $q->whereIn('department_code', $deptCodes))
                    ->sum('target_amount');

                $base = SsSubmission::whereYear('submission_date', $year)
                    ->whereMonth('submission_date', $monthNumber)
                    ->when($deptCodes, fn ($q) => $q->whereIn('department_code', $deptCodes));

                $submitted[] = (clone $base)->count();
                $approved[] = (clone $base)->whereIn('status', ['approved', 'rewarded'])->count();
            }

            return [
                'mode' => 'yearly',
                'labels' => $labels,
                'target' => $target,
                'submitted' => $submitted,
                'approved' => $approved,
            ];
        }

        $target = SsTarget::where('year', $year)
            ->where('month', $month)
            ->when($deptCodes, fn ($q) => $q->whereIn('department_code', $deptCodes))
            ->sum('target_amount');

        $base = SsSubmission::whereYear('submission_date', $year)
            ->whereMonth('submission_date', $month)
            ->when($deptCodes, fn ($q) => $q->whereIn('department_code', $deptCodes));

        $submitted = (clone $base)->count();
        $approved = (clone $base)->whereIn('status', ['approved', 'rewarded'])->count();
        $rewarded = (clone $base)->where('status', 'rewarded')->count();

        return [
            'mode' => 'monthly',
            'labels' => ['Pengajuan', 'Disetujui', 'Reward'],
            'target' => [$target, $target, $target],
            'submitted' => [$submitted, $submitted, $submitted],
            'approved' => [0, $approved, $rewarded],
        ];
    }

    private function calculateSsStats(int $year, ?int $month, string $level, ?string $divCode, ?string $deptCode): array
    {
        $deptCodes = $deptCode ? [$deptCode] : null;
        if (!$deptCodes && $divCode) {
            $deptCodes = Department::where('code_division', $divCode)->pluck('code')->toArray();
        }

        $target = SsTarget::where('year', $year)
            ->when($month, fn ($q) => $q->where('month', $month))
            ->when($deptCodes, fn ($q) => $q->whereIn('department_code', $deptCodes))
            ->sum('target_amount');

        $base = SsSubmission::whereYear('submission_date', $year)
            ->when($month, fn ($q) => $q->whereMonth('submission_date', $month))
            ->when($deptCodes, fn ($q) => $q->whereIn('department_code', $deptCodes));

        $actualToday = SsSubmission::whereDate('submission_date', Carbon::today())
            ->when($deptCodes, fn ($q) => $q->whereIn('department_code', $deptCodes))
            ->count();

        return [
            'target_ss' => (int) $target,
            'total_ss' => (clone $base)->count(),
            'actual_today' => $actualToday,
            'need_review' => (clone $base)->whereIn('status', ['spv_review', 'kdp_review', 'admin_review'])->count(),
            'completed' => (clone $base)->whereIn('status', ['approved', 'rewarded'])->count(),
        ];
    }

    public function masterTargets(Request $request)
    {
        if (!$this->checkAdmin()) {
            abort(403);
        }
        $user = $this->getUser();

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $targets = SsTarget::with('department')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('department', fn ($dq) => $dq->where('name', 'like', "%{$search}%"))
                        ->orWhere('department_code', 'like', "%{$search}%")
                        ->orWhere('year', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate($perPage)
            ->withQueryString();

        $departments = Department::orderBy('name')->get();
        $months = $this->monthNames();
        $years = range(date('Y') - 1, date('Y') + 1);

        return view('ss.admin.master_ss_targets', compact('user', 'targets', 'perPage', 'departments', 'months', 'years'));
    }

    public function storeTarget(Request $request)
    {
        if (!$this->checkAdmin()) {
            abort(403);
        }

        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'department_code' => ['required', Rule::exists('m_departments', 'code')],
            'target_amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        $exists = SsTarget::where('year', $request->year)
            ->where('month', $request->month)
            ->where('department_code', $request->department_code)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Target untuk departemen pada bulan tersebut sudah ada!');
        }

        SsTarget::create($request->only(['year', 'month', 'department_code', 'target_amount', 'description']));

        return redirect()->route('ss.admin.master_targets')->with('success', 'Target SS berhasil ditetapkan!');
    }

    public function updateTarget(Request $request, $id)
    {
        if (!$this->checkAdmin()) {
            abort(403);
        }

        $target = SsTarget::findOrFail($id);

        $request->validate([
            'target_amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        $target->update($request->only(['target_amount', 'description']));

        return redirect()->route('ss.admin.master_targets')->with('success', 'Target SS berhasil diperbarui!');
    }

    public function deleteTarget($id)
    {
        if (!$this->checkAdmin()) {
            abort(403);
        }

        SsTarget::destroy($id);

        return redirect()->route('ss.admin.master_targets')->with('success', 'Target SS berhasil dihapus!');
    }

    public function submissions(Request $request)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $perPage = $request->get('per_page', 20);

        $submissions = $this->filteredSubmissionsQuery($request)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $departments = Department::orderBy('name')->get();
        $years = $this->submissionYears();

        return view('ss.admin.submissions', compact('user', 'submissions', 'departments', 'years'));
    }

    public function exportSubmissionsPdf(Request $request)
    {
        if (!$this->checkAdmin()) abort(403);

        $submissions = $this->filteredSubmissionsQuery($request)
            ->orderBy('created_at', 'desc')
            ->get();

        $department = $request->filled('department_code')
            ? Department::where('code', $request->get('department_code'))->first()
            : null;
        $dateRange = $this->resolveSubmissionDateRange($request);

        $pdf = Pdf::loadView('ss.admin.submissions_pdf', [
            'submissions' => $submissions,
            'filters' => [
                'status' => $request->get('status'),
                'department' => $department,
                'date_from' => $dateRange['from']?->toDateString(),
                'date_to' => $dateRange['to']?->toDateString(),
                'search' => $request->get('search'),
            ],
        ])->setPaper('a4', 'landscape');

        $fileName = 'daftar-ss-'.now()->format('Ymd-His').'.pdf';

        return $pdf->download($fileName);
    }

    private function filteredSubmissionsQuery(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $departmentCode = $request->get('department_code');
        $dateRange = $this->resolveSubmissionDateRange($request);

        return SsSubmission::with(['employee', 'department', 'ldr', 'spv', 'kdp'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($departmentCode, fn ($query) => $query->where('department_code', $departmentCode))
            ->when($dateRange['from'], fn ($query) => $query->whereDate('submission_date', '>=', $dateRange['from']))
            ->when($dateRange['to'], fn ($query) => $query->whereDate('submission_date', '<=', $dateRange['to']))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('employee', fn ($sq) => $sq->where('nama', 'like', "%{$search}%"))
                        ->orWhere('employee_npk', 'like', "%{$search}%")
                        ->orWhere('department_code', 'like', "%{$search}%")
                        ->orWhere('idea_title', 'like', "%{$search}%");
                });
            });
    }

    private function resolveSubmissionDateRange(Request $request): array
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->get('date_from'))->startOfDay()
            : null;
        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->get('date_to'))->endOfDay()
            : null;

        if (!$dateFrom && !$dateTo && ($request->filled('month') || $request->filled('year'))) {
            $month = $request->filled('month') ? (int) $request->get('month') : null;
            $year = $request->filled('year') ? (int) $request->get('year') : (int) date('Y');

            if ($month && $request->filled('year')) {
                $dateFrom = Carbon::create($year, $month, 1)->startOfMonth();
                $dateTo = Carbon::create($year, $month, 1)->endOfMonth();
            } elseif ($request->filled('year')) {
                $dateFrom = Carbon::create($year, 1, 1)->startOfYear();
                $dateTo = Carbon::create($year, 12, 31)->endOfYear();
            } elseif ($month) {
                $dateFrom = Carbon::create($year, $month, 1)->startOfMonth();
                $dateTo = Carbon::create($year, $month, 1)->endOfMonth();
            }
        }

        return ['from' => $dateFrom, 'to' => $dateTo];
    }

    private function submissionYears(): array
    {
        $years = SsSubmission::selectRaw('YEAR(submission_date) as year')
            ->whereNotNull('submission_date')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->toArray();

        return $years ?: [date('Y')];
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

    public function adminReviewIndex(Request $request)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = SsSubmission::with(['employee', 'department', 'spv', 'kdp'])
            ->where('status', 'admin_review');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', fn ($sq) => $sq->where('nama', 'like', "%{$search}%"))
                    ->orWhere('employee_npk', 'like', "%{$search}%")
                    ->orWhere('department_code', 'like', "%{$search}%");
            });
        }

        $submissions = $query->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('ss.admin.review_index', compact('user', 'submissions', 'perPage'));
    }

    public function adminReviewForm($id)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $submission = SsSubmission::with(['employee', 'department', 'spv', 'kdp'])
            ->where('status', 'admin_review')
            ->findOrFail($id);
        $criteria = SsScoringService::criteria();
        $criteriaMaxScores = SsScoringService::criteriaMaxScores();

        return view('ss.admin.review_form', compact('user', 'submission', 'criteria', 'criteriaMaxScores'));
    }

    public function adminReviewStore(Request $request, $id)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $request->validate(array_merge([
            'action' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ], SsScoringService::scoreValidationRules()));

        $submission = SsSubmission::where('status', 'admin_review')->findOrFail($id);
        $scores = $request->action === 'approved'
            ? SsScoringService::normalizeScores($request->input('scores', []))
            : null;
        $total = $scores ? SsScoringService::total($scores) : null;

        $submission->admin_notes = $request->admin_notes;
        $submission->admin_status = $request->action;
        $submission->admin_approved_at = now();
        $submission->admin_npk = $user?->npk;
        $submission->admin_scores = $scores;
        $submission->admin_score_total = $total;
        $submission->score = $total;

        if ($request->action === 'approved') {
            $submission->status = 'approved';
            $submission->final_score = $total;
            $submission->final_approved_at = now();
            $submission->calculated_reward_amount = SsScoringRange::rewardForScore($total);
            $submission->reward_amount = null;
            $submission->paid_at = null;
        } else {
            $submission->status = 'rejected';
            $submission->final_score = null;
            $submission->final_approved_at = null;
            $submission->calculated_reward_amount = null;
            $submission->reward_amount = null;
            $submission->paid_at = null;
        }

        $submission->save();

        return redirect()->route('ss.admin.review.index')->with('success', 'Review admin/komite berhasil disimpan.');
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

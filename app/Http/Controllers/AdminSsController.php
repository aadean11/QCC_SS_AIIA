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
use Illuminate\Database\QueryException;

class AdminSsController extends Controller
{
    private function deleteConstraintResponse(string $label)
    {
        return redirect()->back()->with(
            'error',
            "{$label} tidak bisa dihapus karena sudah digunakan pada data SS atau relasi lain. Hapus atau ubah data terkait terlebih dahulu."
        );
    }

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
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
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
            'description' => 'nullable|string|max:20',
        ], [
            'description.max' => 'Deskripsi maksimal 20 karakter.',
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
            'year' => 'sometimes|required|integer|min:2000|max:2100',
            'month' => 'sometimes|required|integer|min:1|max:12',
            'department_code' => ['sometimes', 'required', Rule::exists('m_departments', 'code')],
            'target_amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:20',
        ], [
            'description.max' => 'Deskripsi maksimal 20 karakter.',
        ]);

        $payload = $request->only(['target_amount', 'description']);
        if ($request->has('year')) {
            $payload['year'] = $request->year;
        }
        if ($request->has('month')) {
            $payload['month'] = $request->month;
        }
        if ($request->has('department_code')) {
            $payload['department_code'] = $request->department_code;
        }

        $exists = SsTarget::where('year', $payload['year'] ?? $target->year)
            ->where('month', $payload['month'] ?? $target->month)
            ->where('department_code', $payload['department_code'] ?? $target->department_code)
            ->where('id', '!=', $target->id)
            ->exists();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Target untuk departemen pada bulan tersebut sudah ada!');
        }

        $target->update($payload);

        return redirect()->route('ss.admin.master_targets')->with('success', 'Target SS berhasil diperbarui!');
    }

    public function deleteTarget($id)
    {
        if (!$this->checkAdmin()) {
            abort(403);
        }

        try {
            SsTarget::destroy($id);

            return redirect()->route('ss.admin.master_targets')->with('success', 'Target SS berhasil dihapus!');
        } catch (QueryException $e) {
            return $this->deleteConstraintResponse('Target SS');
        }
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

    /**
     * Map status to the relevant date column for filtering.
     */
    private function statusDateColumn(?string $status): string
    {
        return match ($status) {
            'approved'     => 'final_approved_at',
            'rewarded'     => 'paid_at',
            'admin_review' => 'admin_approved_at',
            'kdp_review'   => 'kdp_approved_at',
            'spv_review'   => 'spv_approved_at',
            default        => 'submission_date',
        };
    }

    private function filteredSubmissionsQuery(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status') ?: null;
        $departmentCode = $request->get('department_code');
        $dateRange = $this->resolveSubmissionDateRange($request);
        $dateColumn = $this->statusDateColumn($status);
        $hasDateFilter = $dateRange['from'] || $dateRange['to'];

        return SsSubmission::with(['employee', 'department', 'ldr', 'spv', 'kdp'])
            ->when($status, function ($query) use ($status, $dateColumn, $hasDateFilter) {
                // Always filter by the selected status so only matching records are shown.
                // When a date range is also active, additionally require the stage date
                // column to be non-null (the date range itself will narrow it further).
                $query->where('status', $status);
                if ($hasDateFilter) {
                    $query->whereNotNull($dateColumn);
                }
            })
            ->when($departmentCode, fn ($query) => $query->where('department_code', $departmentCode))
            ->when($dateRange['from'], fn ($query) => $query->whereDate($dateColumn, '>=', $dateRange['from']))
            ->when($dateRange['to'], fn ($query) => $query->whereDate($dateColumn, '<=', $dateRange['to']))
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
        $effectiveScore = SsScoringRange::effectiveReviewScore(
            $submission->kdp_score_total,
            $submission->spv_score_total
        );
        $requiresScoring = SsScoringRange::needsAdminScoringAfterKdp($effectiveScore);
        $referenceReward = SsScoringRange::rewardForScore($effectiveScore);

        return view('ss.admin.review_form', compact(
            'user',
            'submission',
            'criteria',
            'criteriaMaxScores',
            'requiresScoring',
            'effectiveScore',
            'referenceReward'
        ));
    }

    public function adminReviewStore(Request $request, $id)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $submission = SsSubmission::where('status', 'admin_review')->findOrFail($id);
        $effectiveScore = SsScoringRange::effectiveReviewScore(
            $submission->kdp_score_total,
            $submission->spv_score_total
        );
        $requiresScoring = SsScoringRange::needsAdminScoringAfterKdp($effectiveScore);

        $rules = [
            'admin_notes' => 'nullable|string|max:20',
        ];
        if ($requiresScoring) {
            $rules = array_merge($rules, SsScoringService::scoreValidationRules());
        }

        $request->validate($rules, [
            'admin_notes.max' => 'Catatan maksimal 20 karakter.',
        ]);

        $previousScores = $submission->kdp_scores ?? $submission->spv_scores ?? [];
        $scores = $requiresScoring
            ? SsScoringService::normalizeScores($request->input('scores', []))
            : $previousScores;
        $total = $scores ? SsScoringService::total($scores) : $effectiveScore;

        $submission->admin_notes = $request->admin_notes;
        $submission->admin_status = 'approved';
        $submission->admin_approved_at = now();
        $submission->admin_npk = $user?->npk;
        $submission->admin_scores = $requiresScoring ? $scores : null;
        $submission->admin_score_total = $requiresScoring ? $total : null;
        $submission->score = $total;
        $submission->status = 'approved';
        $submission->final_score = $total;
        $submission->final_approved_at = now();
        $submission->calculated_reward_amount = SsScoringRange::rewardForScore($total);
        $submission->reward_amount = null;
        $submission->paid_at = null;

        $submission->save();

        return redirect()->route('ss.admin.review.index')->with(
            'success',
            'Review komite berhasil disimpan. Silakan berikan reward melalui menu Hasil & Reward.'
        );
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
        if ($this->hasDuplicateScoringRange($data)) {
            return redirect()->back()->withInput()->with('error', 'Master scoring dengan range, ranking, dan level approval tersebut sudah ada.');
        }
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
        if ($this->hasDuplicateScoringRange($data, (int) $id)) {
            return redirect()->back()->withInput()->with('error', 'Master scoring dengan range, ranking, dan level approval tersebut sudah ada.');
        }
        if ($this->hasOverlappingActiveRange($data, (int) $id)) {
            return redirect()->back()->withInput()->with('error', 'Range total nilai aktif tidak boleh tumpang tindih.');
        }

        $range->update($data);

        return redirect()->route('ss.admin.master_scoring')->with('success', 'Master scoring SS berhasil diperbarui.');
    }

    public function deleteScoring($id)
    {
        if (!$this->checkAdmin()) abort(403);

        try {
            SsScoringRange::findOrFail($id)->delete();

            return redirect()->route('ss.admin.master_scoring')->with('success', 'Master scoring SS berhasil dihapus.');
        } catch (QueryException $e) {
            return $this->deleteConstraintResponse('Master scoring SS');
        }
    }

    private function validateScoringRange(Request $request): array
    {
        $data = $request->validate([
            'min_score' => 'required|integer|min:0',
            'max_score' => 'required|integer|gte:min_score',
            'ranking' => 'required|integer|min:1|max:255',
            'reward_amount' => 'required|numeric|min:0',
            'approver_level' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:20',
            'extra_score_increment' => 'nullable|integer|min:1',
            'extra_reward_increment' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'description.max' => 'Deskripsi maksimal 20 karakter.',
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

    private function hasDuplicateScoringRange(array $data, ?int $ignoreId = null): bool
    {
        return SsScoringRange::where('min_score', $data['min_score'])
            ->where('max_score', $data['max_score'])
            ->where('ranking', $data['ranking'])
            ->where('approver_level', $data['approver_level'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SsSubmission;
use App\Models\SsScoringRange;
use App\Services\SsScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SsApprovalController extends Controller
{
    private function checkAccess(): bool
    {
        return Auth::check() && session('active_role') === 'employee';
    }

    private function getCurrentEmployee()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return $user->employee;
    }

    /**
     * @return array{0: \App\Models\Employee, 1: null}|\Illuminate\Http\RedirectResponse
     */
    private function requireEmployee(string $occupation)
    {
        if (!$this->checkAccess()) {
            return redirect('/login');
        }

        $employee = $this->getCurrentEmployee();
        if (!$employee) {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        if ($employee->occupation !== $occupation) {
            abort(403, 'Halaman ini hanya untuk ' . $occupation . '.');
        }

        return [$employee, null];
    }

    private function baseQuery($employee, string $status)
    {
        return SsSubmission::with(['employee', 'department'])
            ->where('department_code', $employee->getDeptCode())
            ->where('status', $status);
    }

    private function findSubmissionForReview($employee, int $id, string $expectedStatus): SsSubmission
    {
        return SsSubmission::where('id', $id)
            ->where('department_code', $employee->getDeptCode())
            ->where('status', $expectedStatus)
            ->firstOrFail();
    }

    public function indexSpv(Request $request)
    {
        $result = $this->requireEmployee('SPV');
        if (!is_array($result)) {
            return $result;
        }
        [$employee] = $result;
        $user = $employee;

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = $this->baseQuery($employee, 'spv_review');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', fn ($sq) => $sq->where('nama', 'like', "%{$search}%"))
                    ->orWhere('employee_npk', 'like', "%{$search}%");
            });
        }

        $submissions = $query->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('ss.approval.spv_index', compact('user', 'submissions', 'perPage'));
    }

    public function reviewSpvForm($id)
    {
        $result = $this->requireEmployee('SPV');
        if (!is_array($result)) {
            return $result;
        }
        [$employee] = $result;
        $user = $employee;

        $submission = $this->findSubmissionForReview($employee, (int) $id, 'spv_review');
        $criteria = SsScoringService::criteria();
        $criteriaMaxScores = SsScoringService::criteriaMaxScores();
        $decisions = SsScoringService::supervisorDecisions();
        $standardReviews = SsScoringService::standardReviews();
        $implementationStatuses = SsScoringService::implementationStatuses();

        return view('ss.approval.review_spv', compact('user', 'submission', 'criteria', 'criteriaMaxScores', 'decisions', 'standardReviews', 'implementationStatuses'));
    }

    public function reviewSpvStore(Request $request, $id)
    {
        $result = $this->requireEmployee('SPV');
        if (!is_array($result)) {
            return $result;
        }
        [$employee] = $result;

        $request->validate(array_merge([
            'action' => 'required|in:approved,rejected',
            'supervisor_decision' => ['required', 'string', Rule::in(array_keys(SsScoringService::supervisorDecisions()))],
            'standard_review' => ['nullable', 'string', Rule::in(array_keys(SsScoringService::standardReviews()))],
            'spv_notes' => 'nullable|string|max:500',
            'supervisor_reason' => 'nullable|string|max:1000',
        ], SsScoringService::scoreValidationRules()));

        $submission = $this->findSubmissionForReview($employee, (int) $id, 'spv_review');
        $scores = $request->action === 'approved'
            ? SsScoringService::normalizeScores($request->input('scores', []))
            : null;
        $total = $scores ? SsScoringService::total($scores) : null;

        $submission->spv_notes = $request->spv_notes;
        $submission->spv_status = $request->action;
        $submission->spv_approved_at = now();
        $submission->spv_npk = $employee->npk;
        $submission->supervisor_decision = $request->supervisor_decision;
        $submission->supervisor_reason = $request->supervisor_reason;
        $submission->standard_review = $request->standard_review;
        $submission->spv_scores = $scores;
        $submission->spv_score_total = $total;
        $submission->score = $total;

        if ($request->action === 'approved') {
            $submission->status = 'kdp_review';
            $submission->final_score = null;
            $submission->final_approved_at = null;
            $submission->calculated_reward_amount = null;
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

        $message = match ($submission->status) {
            'kdp_review' => 'Review SPV berhasil disimpan. SS diteruskan ke KDP/Manager.',
            default => 'Review SPV berhasil disimpan. SS ditolak.',
        };

        return redirect()->route('ss.approval.spv')->with('success', $message);
    }

    public function indexKdp(Request $request)
    {
        $result = $this->requireEmployee('KDP');
        if (!is_array($result)) {
            return $result;
        }
        [$employee] = $result;
        $user = $employee;

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = $this->baseQuery($employee, 'kdp_review');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', fn ($sq) => $sq->where('nama', 'like', "%{$search}%"))
                    ->orWhere('employee_npk', 'like', "%{$search}%");
            });
        }

        $submissions = $query->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('ss.approval.kdp_index', compact('user', 'submissions', 'perPage'));
    }

    public function reviewKdpForm($id)
    {
        $result = $this->requireEmployee('KDP');
        if (!is_array($result)) {
            return $result;
        }
        [$employee] = $result;
        $user = $employee;

        $submission = $this->findSubmissionForReview($employee, (int) $id, 'kdp_review');
        $criteria = SsScoringService::criteria();
        $criteriaMaxScores = SsScoringService::criteriaMaxScores();
        $requiresScoring = SsScoringRange::needsKdpScoringAfterSpv($submission->spv_score_total);

        return view('ss.approval.review_kdp', compact('user', 'submission', 'criteria', 'criteriaMaxScores', 'requiresScoring'));
    }

    public function reviewKdpStore(Request $request, $id)
    {
        $result = $this->requireEmployee('KDP');
        if (!is_array($result)) {
            return $result;
        }
        [$employee] = $result;

        $submission = $this->findSubmissionForReview($employee, (int) $id, 'kdp_review');
        $requiresScoring = SsScoringRange::needsKdpScoringAfterSpv($submission->spv_score_total);
        $rules = [
            'action' => 'required|in:approved,rejected',
            'kdp_notes' => 'nullable|string|max:500',
        ];
        if ($requiresScoring) {
            $rules = array_merge($rules, SsScoringService::scoreValidationRules());
        }

        $request->validate($rules);
        $scores = $request->action === 'approved'
            ? ($requiresScoring ? SsScoringService::normalizeScores($request->input('scores', [])) : ($submission->spv_scores ?? []))
            : null;
        $total = $scores ? SsScoringService::total($scores) : null;

        $submission->kdp_notes = $request->kdp_notes;
        $submission->kdp_status = $request->action;
        $submission->kdp_approved_at = now();
        $submission->kdp_npk = $employee->npk;
        $submission->kdp_scores = $requiresScoring ? $scores : null;
        $submission->kdp_score_total = $requiresScoring ? $total : null;
        $submission->final_score = $total;
        $submission->score = $total;

        if ($request->action === 'approved') {
            if ($requiresScoring && SsScoringRange::needsAdminScoringAfterKdp($total)) {
                $submission->status = 'admin_review';
                $submission->final_score = null;
                $submission->final_approved_at = null;
                $submission->calculated_reward_amount = null;
                $submission->reward_amount = null;
                $submission->paid_at = null;
            } else {
                $rewardAmount = SsScoringRange::rewardForScore($total);

                $submission->status = 'approved';
                $submission->final_score = $total;
                $submission->final_approved_at = now();
                $submission->calculated_reward_amount = $rewardAmount;
                $submission->reward_amount = null;
                $submission->paid_at = null;
            }
        } else {
            $submission->status = 'rejected';
            $submission->calculated_reward_amount = null;
            $submission->reward_amount = null;
            $submission->paid_at = null;
        }

        $submission->save();

        return redirect()->route('ss.approval.kdp')->with('success', 'Review KDP berhasil disimpan.');
    }
}

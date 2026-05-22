<?php

namespace App\Http\Controllers;

use App\Models\SsSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $query = $this->baseQuery($employee, 'assessed');

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

        $submission = $this->findSubmissionForReview($employee, (int) $id, 'assessed');

        return view('ss.approval.review_spv', compact('user', 'submission'));
    }

    public function reviewSpvStore(Request $request, $id)
    {
        $result = $this->requireEmployee('SPV');
        if (!is_array($result)) {
            return $result;
        }
        [$employee] = $result;

        $request->validate([
            'action' => 'required|in:approved,rejected',
            'spv_notes' => 'nullable|string|max:500',
        ]);

        $submission = $this->findSubmissionForReview($employee, (int) $id, 'assessed');

        $submission->spv_notes = $request->spv_notes;
        $submission->spv_status = $request->action;
        $submission->spv_approved_at = now();
        $submission->spv_npk = $employee->npk;
        $submission->status = $request->action === 'approved' ? 'kdp_review' : 'rejected';
        $submission->save();

        return redirect()->route('ss.approval.spv')->with('success', 'Review SPV berhasil disimpan.');
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

        return view('ss.approval.review_kdp', compact('user', 'submission'));
    }

    public function reviewKdpStore(Request $request, $id)
    {
        $result = $this->requireEmployee('KDP');
        if (!is_array($result)) {
            return $result;
        }
        [$employee] = $result;

        $request->validate([
            'action' => 'required|in:approved,rejected',
            'kdp_notes' => 'nullable|string|max:500',
        ]);

        $submission = $this->findSubmissionForReview($employee, (int) $id, 'kdp_review');

        $submission->kdp_notes = $request->kdp_notes;
        $submission->kdp_status = $request->action;
        $submission->kdp_approved_at = now();
        $submission->kdp_npk = $employee->npk;

        if ($request->action === 'approved') {
            $submission->status = 'approved';
            $submission->final_approved_at = now();
        } else {
            $submission->status = 'rejected';
        }

        $submission->save();

        return redirect()->route('ss.approval.kdp')->with('success', 'Review KDP berhasil disimpan.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SsSubmission;
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

    public function dashboard()
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        // Statistik card
        $total = SsSubmission::count();
        $pendingScore = SsSubmission::whereNull('score')->count();
        $approved = SsSubmission::where('status', 'approved')->count();
        $rewarded = SsSubmission::where('status', 'rewarded')->count();

        // Data grafik batang: perkembangan SS per bulan (tahun berjalan)
        $monthlyData = SsSubmission::selectRaw('DATE_FORMAT(submission_date, "%b") as month, COUNT(*) as total')
            ->whereYear('submission_date', date('Y'))
            ->groupBy('month')
            ->orderByRaw('MIN(submission_date)')
            ->get();

        // Data grafik pie: distribusi status
        $statusData = SsSubmission::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Mapping status ke label yang lebih rapi untuk grafik
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

        return view('ss.admin.dashboard', compact(
            'user', 'total', 'pendingScore', 'approved', 'rewarded',
            'monthlyData', 'statusData'
        ));
    }

    public function submissions(Request $request)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');
        $status = $request->get('status');

        $query = SsSubmission::with(['employee', 'spv', 'kdp']);

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

        $submission = SsSubmission::with(['employee', 'spv', 'kdp'])->findOrFail($id);

        return view('ss.admin.show', compact('user', 'submission'));
    }

    public function assessForm($id)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $submission = SsSubmission::findOrFail($id);

        return view('ss.admin.assess', compact('user', 'submission'));
    }

    public function assessStore(Request $request, $id)
    {
        if (!$this->checkAdmin()) abort(403);

        $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $submission = SsSubmission::findOrFail($id);
        $submission->score = $request->score;
        $submission->notes = $request->notes;
        $submission->status = 'assessed';
        $submission->save();

        return redirect()->route('ss.admin.submissions')->with('success', 'Nilai berhasil disimpan.');
    }

    public function reviewSpvForm($id)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $submission = SsSubmission::findOrFail($id);

        return view('ss.admin.review_spv', compact('user', 'submission'));
    }

    public function reviewSpvStore(Request $request, $id)
    {
        if (!$this->checkAdmin()) abort(403);

        $request->validate([
            'action' => 'required|in:approved,rejected',
            'spv_notes' => 'nullable|string',
        ]);

        $submission = SsSubmission::findOrFail($id);
        $submission->spv_notes = $request->spv_notes;
        $submission->spv_status = $request->action;
        $submission->spv_approved_at = now();
        $submission->spv_id = Auth::user()->employee->id ?? null;

        if ($request->action === 'approved') {
            $submission->status = 'kdp_review';
        } else {
            $submission->status = 'rejected';
        }

        $submission->save();

        return redirect()->route('ss.admin.submissions')->with('success', 'Review SPV berhasil.');
    }

    public function reviewKdpForm($id)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $submission = SsSubmission::findOrFail($id);

        return view('ss.admin.review_kdp', compact('user', 'submission'));
    }

    public function reviewKdpStore(Request $request, $id)
    {
        if (!$this->checkAdmin()) abort(403);

        $request->validate([
            'action' => 'required|in:approved,rejected',
            'kdp_notes' => 'nullable|string',
        ]);

        $submission = SsSubmission::findOrFail($id);
        $submission->kdp_notes = $request->kdp_notes;
        $submission->kdp_status = $request->action;
        $submission->kdp_approved_at = now();
        $submission->kdp_id = Auth::user()->employee->id ?? null;

        if ($request->action === 'approved') {
            $submission->status = 'approved';
            $submission->final_approved_at = now();
        } else {
            $submission->status = 'rejected';
        }

        $submission->save();

        return redirect()->route('ss.admin.submissions')->with('success', 'Review KDP berhasil.');
    }

    public function rewardForm($id)
    {
        if (!$this->checkAdmin()) abort(403);
        $user = $this->getUser();

        $submission = SsSubmission::findOrFail($id);

        return view('ss.admin.reward', compact('user', 'submission'));
    }

    public function rewardStore(Request $request, $id)
    {
        if (!$this->checkAdmin()) abort(403);

        $request->validate([
            'reward_amount' => 'required|numeric|min:0',
        ]);

        $submission = SsSubmission::findOrFail($id);
        $submission->reward_amount = $request->reward_amount;
        $submission->paid_at = now();
        $submission->status = 'rewarded';
        $submission->save();

        return redirect()->route('ss.admin.submissions')->with('success', 'Reward berhasil diberikan.');
    }
}
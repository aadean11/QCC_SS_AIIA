<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QccCircleStepTransaction;
use App\Models\Employee;
use App\Models\User;
use App\Models\QccCircle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QccApprovalController extends Controller
{
    private function getCurrentUser()
    {
        $npk = session('auth_npk');
        return Employee::with('job')->where('npk', $npk)->first() 
               ?? User::where('npk', $npk)->first();
    }

    public function index()
    {
        $user = $this->getCurrentUser();
        if (!$user) return redirect('/login');

        $query = QccCircleStepTransaction::with(['circle', 'step', 'uploader']);

        if ($user->occupation === 'KDP') {
            $query->where('status', 'WAITING KADEPT');
        } elseif ($user->occupation === 'SPV') {
            $query->where('status', 'WAITING SPV');
        } else {
            return redirect()->route('welcome')->with('warning', 'Akses ditolak.');
        }

        $pendingSteps = $query->orderBy('created_at', 'asc')->get();
        return view('qcc.approval.index', compact('user', 'pendingSteps'));
    }

    // Progres Approval (Step 1-8)
    public function process(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'required_if:action,reject'
        ]);

        $step = QccCircleStepTransaction::findOrFail($id);
        $user = $this->getCurrentUser();

        if ($request->action === 'approve') {
            if ($user->occupation === 'SPV') {
                $step->update(['status' => 'WAITING KADEPT']);
                return redirect()->back()->with('success', 'Disetujui SPV. Menunggu KDP.');
            } 
            if ($user->occupation === 'KDP') {
                $step->update(['status' => 'APPROVED']);
                return redirect()->back()->with('success', 'Disetujui KDP. Status APPROVED.');
            }
        } else {
            $status = ($user->occupation === 'KDP') ? 'REJECTED BY KDP' : 'REJECTED BY SPV';
            $step->update([
                'status' => $status,
                'spv_note' => ($user->occupation === 'SPV') ? $request->note : $step->spv_note,
                'kadept_note' => ($user->occupation === 'KDP') ? $request->note : $step->kadept_note,
            ]);
            return redirect()->back()->with('success', 'Progres ditolak.');
        }
        return redirect()->back()->with('error', 'Otoritas tidak valid.');
    }

    public function indexCircle()
    {
        $user = $this->getCurrentUser();
        $status = ($user->occupation === 'KDP') ? 'WAITING KDP' : 'WAITING SPV';
        $pendingCircles = QccCircle::with(['members.employee', 'department'])->where('status', $status)->get();
        return view('qcc.approval.circle_index', compact('user', 'pendingCircles'));
    }

    // Circle Approval (Pendaftaran Awal)
    public function processCircle(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'required_if:action,reject'
        ]);

        $circle = QccCircle::findOrFail($id);
        $user = $this->getCurrentUser();

        if ($request->action === 'approve') {
            if ($user->occupation === 'SPV') {
                $circle->update(['status' => 'WAITING KDP']);
                return redirect()->back()->with('success', 'Circle disetujui SPV. Menunggu KDP.');
            }
            if ($user->occupation === 'KDP') {
                $circle->update(['status' => 'ACTIVE']);
                return redirect()->back()->with('success', 'Circle telah ACTIVE.');
            }
        } else {
            $status = ($user->occupation === 'KDP') ? 'REJECTED BY KDP' : 'REJECTED BY SPV';
            $circle->update([
                'status' => $status,
                'spv_note' => ($user->occupation === 'SPV') ? $request->note : $circle->spv_note,
                'kdp_note' => ($user->occupation === 'KDP') ? $request->note : $circle->kdp_note,
            ]);
            return redirect()->back()->with('success', 'Pendaftaran Circle ditolak.');
        }
        return redirect()->back()->with('error', 'Tindakan gagal.');
    }
}
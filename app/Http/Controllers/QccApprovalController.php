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
        // PENTING: Harus muat relasi subSection dan section
        return Employee::with(['subSection.section', 'job'])->where('npk', $npk)->first() 
               ?? User::where('npk', $npk)->first();
    }

    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        $myDept = $user->getDeptCode(); // Contoh: 'PPC'

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        // Query: Filter Transaksi berdasarkan department_code milik Circle
        $query = QccCircleStepTransaction::with(['circle.department', 'step', 'uploader'])
            ->whereHas('circle', function($q) use ($myDept) {
                $q->where('department_code', $myDept);
            });

        // Logika Role
        if ($user->occupation === 'KDP') {
            $query->whereIn('status', ['WAITING KADEPT', 'APPROVED', 'REJECTED BY KDP']);
        } elseif ($user->occupation === 'SPV') {
            $query->whereIn('status', ['WAITING SPV', 'WAITING KADEPT', 'REJECTED BY SPV', 'APPROVED']);
        } else {
            return redirect()->route('welcome')->with('error', 'Akses ditolak.');
        }

        // Search logic
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('circle', fn($sq) => $sq->where('circle_name', 'like', "%{$search}%"))
                  ->orWhereHas('uploader', fn($sq) => $sq->where('nama', 'like', "%{$search}%"));
            });
        }

        $pendingSteps = $query->orderBy('updated_at', 'desc')->paginate($perPage)->withQueryString();

        return view('qcc.approval.index', compact('user', 'pendingSteps', 'perPage'));
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

    public function indexCircle(Request $request)
    {
        $user = $this->getCurrentUser();
        $myDept = $user->getDeptCode();

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        // Query: Filter Pendaftaran Circle berdasarkan department_code
        $query = QccCircle::with(['members.employee', 'department'])
            ->where('department_code', $myDept);

        if ($user->occupation === 'KDP') {
            $query->whereIn('status', ['WAITING KDP', 'ACTIVE', 'REJECTED BY KDP']);
        } elseif ($user->occupation === 'SPV') {
            $query->whereIn('status', ['WAITING SPV', 'WAITING KDP', 'REJECTED BY SPV', 'ACTIVE']);
        }

        if ($search) {
            $query->where('circle_name', 'like', "%{$search}%")
                  ->orWhere('circle_code', 'like', "%{$search}%");
        }

        $pendingCircles = $query->orderBy('updated_at', 'desc')->paginate($perPage)->withQueryString();

        return view('qcc.approval.circle_index', compact('user', 'pendingCircles', 'perPage'));
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
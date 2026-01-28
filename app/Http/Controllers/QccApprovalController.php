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
    /**
     * Mendapatkan data user yang sedang login secara konsisten.
     */
    private function getCurrentUser()
    {
        $npk = session('auth_npk');
        return Employee::with('job')->where('npk', $npk)->first() 
               ?? User::where('npk', $npk)->first();
    }

    public function index()
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            Auth::logout();
            return redirect('/login');
        }

        $query = QccCircleStepTransaction::with(['circle', 'step', 'uploader']);

        // LOGIC FILTER BERDASARKAN OCCUPATION
        if ($user->occupation === 'KDP') {
            // Kepala Departemen melihat yang sudah lolos dari SPV
            $query->where('status', 'WAITING KADEPT');
        } elseif ($user->occupation === 'SPV') {
            // Supervisor melihat data yang baru masuk
            $query->where('status', 'WAITING SPV');
        } else {
            return redirect()->route('welcome')->with('warning', 'Menu ini hanya untuk SPV atau Kepala Departemen.');
        }

        $pendingSteps = $query->orderBy('created_at', 'asc')->get();

        return view('qcc.approval.index', compact('user', 'pendingSteps'));
    }

    public function process(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'required_if:action,reject'
        ], [
            'note.required_if' => 'Alasan penolakan wajib diisi jika Anda menolak progres.'
        ]);

        $step = QccCircleStepTransaction::findOrFail($id);
        $user = $this->getCurrentUser();
        $action = $request->action;

        if ($action === 'approve') {
            if ($user->isSpv()) {
                // SPV Approve -> Naik ke KDP
                $step->update(['status' => 'WAITING KADEPT']);
                return redirect()->back()->with('success', 'Dokumen disetujui Supervisor. Menunggu persetujuan Kepala Departemen.');
            } 
            
            if ($user->isKadept()) {
                // KDP Approve -> Final APPROVED
                $step->update(['status' => 'APPROVED']);
                return redirect()->back()->with('success', 'Dokumen disetujui Kepala Departemen. Progres selesai.');
            }
        } else {
            // LOGIKA REJECT
            $newStatus = $user->isKadept() ? 'REJECTED BY KDP' : 'REJECTED BY SPV';
            
            $step->update([
                'status' => $newStatus,
                'spv_note' => $user->isSpv() ? $request->note : $step->spv_note,
                'kadept_note' => $user->isKadept() ? $request->note : $step->kadept_note,
            ]);
            
            return redirect()->back()->with('success', 'Progres telah ditolak.');
        }

        return redirect()->back()->with('error', 'Tindakan tidak valid atau Anda tidak memiliki otoritas.');
    }


    // --- Menampilkan Daftar Circle yang Butuh Approval ---
    public function indexCircle()
    {
        $user = $this->getCurrentUser();
        $status = ($user->occupation === 'KDP') ? 'WAITING KDP' : 'WAITING SPV';

        $pendingCircles = QccCircle::with(['members.employee', 'department'])
            ->where('status', $status)
            ->get();

        return view('qcc.approval.circle_index', compact('user', 'pendingCircles'));
    }

    // --- Proses Approval/Reject Circle ---
    public function processCircle(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'required_if:action,reject'
        ]);

        $circle = QccCircle::findOrFail($id);
        $user = $this->getCurrentUser();

        if ($request->action === 'approve') {
            if ($user->isSpv()) {
                $circle->update(['status' => 'WAITING KDP']);
                return redirect()->back()->with('success', 'Circle disetujui SPV. Menunggu Kepala Departemen.');
            }
            if ($user->isKadept()) {
                $circle->update(['status' => 'ACTIVE']);
                return redirect()->back()->with('success', 'Circle telah ACTIVE. Karyawan sudah bisa membuat Tema.');
            }
        } else {
            $status = $user->isKadept() ? 'REJECTED BY KDP' : 'REJECTED BY SPV';
            $circle->update([
                'status' => $status,
                'spv_note' => $user->isSpv() ? $request->note : $circle->spv_note,
                'kdp_note' => $user->isKadept() ? $request->note : $circle->kdp_note,
            ]);
            return redirect()->back()->with('success', 'Pendaftaran Circle ditolak.');
        }
    }
}
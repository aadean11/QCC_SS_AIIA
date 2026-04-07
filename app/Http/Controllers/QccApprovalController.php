<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QccCircleStepTransaction;
use App\Models\Employee;
use App\Models\QccCircle;
use App\Models\Department;
use App\Models\QccTheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QccApprovalController extends Controller
{
    /**
     * Periksa apakah user login dan memiliki role employee (bukan admin)
     */
    private function checkAccess(): bool
    {
        return Auth::check() && session('active_role') === 'employee';
    }

    /**
     * Ambil data employee dari user yang sedang login.
     * Jika tidak ada relasi employee, return null.
     */
    private function getCurrentEmployee()
    {
        $user = Auth::user();
        if (!$user) return null;

        // Gunakan relasi employee yang sudah didefinisikan di model User
        return $user->employee;
    }

    /**
     * Halaman daftar tema QCC yang perlu approval (SPV/KDP)
     */
    public function index(Request $request)
    {
        if (!$this->checkAccess()) {
            return redirect('/login');
        }

        $employee = $this->getCurrentEmployee();
        if (!$employee) {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Untuk kompatibilitas view, gunakan $user
        $user = $employee;

        $myDept = $user->getDeptCode();
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = QccTheme::with(['circle', 'stepProgress.step', 'stepProgress.uploader'])
            ->whereHas('circle', function ($q) use ($myDept) {
                $q->where('department_code', $myDept);
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('theme_name', 'like', "%{$search}%")
                    ->orWhereHas('circle', function ($sq) use ($search) {
                        $sq->where('circle_name', 'like', "%{$search}%");
                    });
            });
        }

        $pendingThemes = $query->orderBy('updated_at', 'desc')->paginate($perPage)->withQueryString();

        return view('qcc.approval.index', compact('user', 'pendingThemes', 'perPage'));
    }

    /**
     * Proses approval/reject per step transaction
     */
    public function process(Request $request, $id)
    {
        if (!$this->checkAccess()) {
            return redirect('/login');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'note'   => 'nullable|string|max:500'
        ]);

        $step = QccCircleStepTransaction::findOrFail($id);
        $employee = $this->getCurrentEmployee();

        if (!$employee) {
            return redirect('/login')->with('error', 'Sesi telah berakhir.');
        }

        // Pastikan departemen sesuai
        if ($step->circle->department_code !== $employee->getDeptCode()) {
            return redirect()->back()->with('error', 'Akses ditolak: Departemen berbeda.');
        }

        if ($request->action === 'approve') {
            if ($employee->occupation === 'SPV') {
                $step->update([
                    'status'         => 'WAITING KDP',
                    'spv_approved_at' => now(),
                    'spv_note'       => $request->note
                ]);
            } elseif ($employee->occupation === 'KDP') {
                $step->update([
                    'status'         => 'APPROVED',
                    'kdp_approved_at' => now(),
                    'kdp_note'       => $request->note
                ]);
            } else {
                return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk approve.');
            }
        } else {
            // Reject
            $status = ($employee->occupation === 'KDP') ? 'REJECTED BY KDP' : 'REJECTED BY SPV';
            $step->update([
                'status'   => $status,
                'kdp_note' => $request->note
            ]);
        }

        return redirect()->back()->with('success', 'Data berhasil diproses.');
    }

    /**
     * Halaman daftar circle yang perlu approval
     */
    public function indexCircle(Request $request)
    {
        if (!$this->checkAccess()) {
            return redirect('/login');
        }

        $employee = $this->getCurrentEmployee();
        if (!$employee) {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Untuk kompatibilitas view, gunakan $user
        $user = $employee;

        $myDept = $user->getDeptCode();
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = QccCircle::with(['members.employee'])
            ->where('department_code', $myDept);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('circle_name', 'like', "%{$search}%")
                    ->orWhere('circle_code', 'like', "%{$search}%");
            });
        }

        $pendingCircles = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        return view('qcc.approval.circle_index', compact('user', 'pendingCircles', 'perPage'));
    }

    /**
     * Proses approval/reject untuk circle (pengajuan circle baru)
     */
    public function processCircle(Request $request, $id)
    {
        if (!$this->checkAccess()) {
            return redirect('/login');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'note'   => 'nullable|string|max:500'
        ]);

        $circle = QccCircle::findOrFail($id);
        $employee = $this->getCurrentEmployee();

        if (!$employee) {
            return redirect('/login')->with('error', 'Sesi telah berakhir.');
        }

        if ($circle->department_code !== $employee->getDeptCode()) {
            return redirect()->back()->with('error', 'Akses ditolak: Departemen berbeda.');
        }

        if ($request->action === 'approve') {
            if ($employee->occupation === 'SPV') {
                $circle->update([
                    'status'          => 'WAITING KDP',
                    'spv_approved_at' => now(),
                    'spv_note'        => $request->note
                ]);
            } elseif ($employee->occupation === 'KDP') {
                $circle->update([
                    'status'          => 'ACTIVE',
                    'kdp_approved_at' => now(),
                    'kdp_note'        => $request->note
                ]);
            } else {
                return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk approve.');
            }
        } else {
            // Reject
            $status = ($employee->occupation === 'KDP') ? 'REJECTED BY KDP' : 'REJECTED BY SPV';
            $circle->update([
                'status'   => $status,
                'kdp_note' => $request->note
            ]);
        }

        return redirect()->back()->with('success', 'Data berhasil diproses.');
    }
}
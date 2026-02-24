<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QccCircleStepTransaction;
use App\Models\Employee;
use App\Models\QccCircle;
use Illuminate\Support\Facades\Log;

class QccApprovalController extends Controller
{
    private function getCurrentUser()
    {
        $npk = session('auth_npk');
        $user = Employee::with(['subSection.section', 'section', 'job'])->where('npk', $npk)->first();

        if ($user) {
            $dept = $user->getDeptCode();
            Log::info("Debug Dept Detection", [
                "NPK" => $user->npk,
                "Jabatan" => $user->occupation,
                "Dept_Terdeteksi" => $dept
            ]);
        }

        return $user;
    }

    // --- APPROVAL PROGRES (STEP 1-8) ---
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        $myDept = $user->getDeptCode();
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        // Query Dasar: Kita ambil TEMA, bukan transaksi per langkah
        $query = \App\Models\QccTheme::with(['circle', 'stepProgress.step', 'stepProgress.uploader'])
            ->whereHas('circle', function($q) use ($myDept) {
                $q->where('department_code', $myDept);
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('theme_name', 'like', "%{$search}%")
                ->orWhereHas('circle', function($sq) use ($search) {
                    $sq->where('circle_name', 'like', "%{$search}%");
                });
            });
        }

        // Urutkan berdasarkan yang terbaru aktif
        $pendingThemes = $query->orderBy('updated_at', 'desc')
                            ->paginate($perPage)
                            ->withQueryString();

        return view('qcc.approval.index', compact('user', 'pendingThemes', 'perPage'));
    }

    // Progres Approval (Step 1-8)
    public function process(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:500'
        ]);

        $step = QccCircleStepTransaction::findOrFail($id);
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return redirect('/login')->with('error', 'Sesi telah berakhir.');
        }

        // Verifikasi: apakah user berhak menyetujui circle di department ini?
        $circleDept = $step->circle->department_code ?? null;
        $userDept = $user->getDeptCode();
        
        if ($circleDept !== $userDept) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses untuk menyetujui progres di department ini.');
        }

        if ($request->action === 'approve') {
            if ($user->occupation === 'SPV') {
                $step->update([
                    'status' => 'WAITING KDP',
                    'spv_approved_at' => now(),
                    'spv_note' => $request->note
                ]);
                return redirect()->back()->with('success', 'Disetujui SPV. Menunggu persetujuan KDP.');
            } 
            if ($user->occupation === 'KDP') {
                $step->update([
                    'status' => 'APPROVED',
                    'kdp_approved_at' => now(),
                    'kdp_note' => $request->note
                ]);
                return redirect()->back()->with('success', 'Disetujui KDP. Status APPROVED.');
            }
        } else {
            // Action: reject
            if ($user->occupation === 'SPV') {
                $step->update([
                    'status' => 'REJECTED BY SPV',
                    'spv_rejected_at' => now(),
                    'spv_note' => $request->note
                ]);
            } elseif ($user->occupation === 'KDP') {
                $step->update([
                    'status' => 'REJECTED BY KDP',
                    'kdp_rejected_at' => now(),
                    'kdp_note' => $request->note
                ]);
            }
            
            return redirect()->back()->with('success', 'Progres telah ditolak.');
        }
    }

    // --- APPROVAL CIRCLE BARU ---
    public function indexCircle(Request $request)
    {
        $user = $this->getCurrentUser();
        $myDept = $user->getDeptCode();
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        // Ambil SEMUA data Circle di departemen user (Tanpa filter status pending saja)
        $query = QccCircle::with(['members.employee'])
            ->where('department_code', $myDept);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('circle_name', 'like', "%{$search}%")
                ->orWhere('circle_code', 'like', "%{$search}%");
            });
        }

        $pendingCircles = $query->orderBy('created_at', 'desc')
                            ->paginate($perPage)
                            ->withQueryString();

        return view('qcc.approval.circle_index', compact('user', 'pendingCircles', 'perPage'));
    }

    public function processCircle(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:500'
        ]);

        $circle = QccCircle::findOrFail($id);
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return redirect('/login')->with('error', 'Sesi telah berakhir.');
        }

        // Verifikasi department
        if ($circle->department_code !== $user->getDeptCode()) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses untuk menyetujui circle di department ini.');
        }

        if ($request->action === 'approve') {
            if ($user->occupation === 'SPV') {
                $circle->update([
                    'status' => 'WAITING KDP',
                    'spv_approved_at' => now(),
                    'spv_note' => $request->note
                ]);
                return redirect()->back()->with('success', 'Circle disetujui SPV. Menunggu persetujuan KDP.');
            }
            if ($user->occupation === 'KDP') {
                $circle->update([
                    'status' => 'ACTIVE',
                    'kdp_approved_at' => now(),
                    'kdp_note' => $request->note
                ]);
                return redirect()->back()->with('success', 'Circle telah diaktifkan (ACTIVE).');
            }
        } else {
            // Action: reject
            $status = ($user->occupation === 'KDP') ? 'REJECTED BY KDP' : 'REJECTED BY SPV';
            
            if ($user->occupation === 'SPV') {
                $circle->update([
                    'status' => $status,
                    'spv_rejected_at' => now(),
                    'spv_note' => $request->note
                ]);
            } elseif ($user->occupation === 'KDP') {
                $circle->update([
                    'status' => $status,
                    'kdp_rejected_at' => now(),
                    'kdp_note' => $request->note
                ]);
            }
            
            return redirect()->back()->with('success', 'Pendaftaran Circle ditolak.');
        }
    }
}
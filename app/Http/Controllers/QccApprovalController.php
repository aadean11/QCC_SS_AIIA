<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QccCircleStepTransaction;
use App\Models\Employee;
use App\Models\QccCircle;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QccApprovalController extends Controller
{
    /**
     * Helper: Ambil profil Employee berdasarkan User yang sedang login via Auth.
     * Mencegah NPK berubah-ubah karena mengambil langsung dari identitas Laravel Auth.
     */
    private function getCurrentUser()
    {
        if (!Auth::check()) return null;

        // Ambil NPK langsung dari sumber autentikasi (tabel users)
        $npk = Auth::user()->npk;
        
        $user = Employee::with([
            'subSection.section.department', 
            'section.department', 
            'job'
        ])->where('npk', $npk)->first();

        if ($user) {
            // Logika sinkronisasi session jika diperlukan
            if (session('auth_npk') !== $user->npk) {
                session(['auth_npk' => $user->npk]);
            }

            Log::info("Approval Access Debug", [
                "NPK" => $user->npk,
                "Jabatan" => $user->occupation,
                "Dept_Code" => $user->getDeptCode()
            ]);
        }

        return $user;
    }

    /**
     * Helper: Proteksi Role.
     * Memastikan user yang masuk adalah role 'employee' (SPV/KDP masuk lewat jalur ini).
     */
    private function checkAccess()
    {
        if (!Auth::check() || session('active_role') !== 'employee') {
            return false;
        }
        return true;
    }

    // --- APPROVAL PROGRES (STEP 1-8) ---
    public function index(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');
        
        $user = $this->getCurrentUser();
        if (!$user) return redirect('/login');

        $myDept = $user->getDeptCode();
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

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

        $pendingThemes = $query->orderBy('updated_at', 'desc')
                            ->paginate($perPage)
                            ->withQueryString();

        return view('qcc.approval.index', compact('user', 'pendingThemes', 'perPage'));
    }

    // Progres Approval (Step 1-8)
    public function process(Request $request, $id)
    {
        if (!$this->checkAccess()) return redirect('/login');

        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:500'
        ]);

        $step = QccCircleStepTransaction::findOrFail($id);
        $user = $this->getCurrentUser();
        
        if (!$user) return redirect('/login')->with('error', 'Sesi telah berakhir.');

        // Verifikasi Departemen
        $circleDept = $step->circle->department_code ?? null;
        $userDept = $user->getDeptCode();
        
        if ($circleDept !== $userDept) {
            return redirect()->back()->with('error', 'Akses ditolak: Departemen tidak sesuai.');
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
            $status = ($user->occupation === 'KDP') ? 'REJECTED BY KDP' : 'REJECTED BY SPV';
            
            $updateData = ['status' => $status];
            if ($user->occupation === 'SPV') {
                $updateData['spv_rejected_at'] = now();
                $updateData['spv_note'] = $request->note;
            } else {
                $updateData['kdp_rejected_at'] = now();
                $updateData['kdp_note'] = $request->note;
            }

            $step->update($updateData);
            return redirect()->back()->with('success', 'Progres telah ditolak.');
        }
    }

    // --- APPROVAL CIRCLE BARU ---
    public function indexCircle(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');

        $user = $this->getCurrentUser();
        if (!$user) return redirect('/login');

        $myDept = $user->getDeptCode();
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

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
        if (!$this->checkAccess()) return redirect('/login');

        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:500'
        ]);

        $circle = QccCircle::findOrFail($id);
        $user = $this->getCurrentUser();
        
        if (!$user) return redirect('/login')->with('error', 'Sesi telah berakhir.');

        // Verifikasi department
        if ($circle->department_code !== $user->getDeptCode()) {
            return redirect()->back()->with('error', 'Akses ditolak: Departemen tidak sesuai.');
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
            
            $updateData = ['status' => $status];
            if ($user->occupation === 'SPV') {
                $updateData['spv_rejected_at'] = now();
                $updateData['spv_note'] = $request->note;
            } else {
                $updateData['kdp_rejected_at'] = now();
                $updateData['kdp_note'] = $request->note;
            }
            
            $circle->update($updateData);
            return redirect()->back()->with('success', 'Pendaftaran Circle ditolak.');
        }
    }
}
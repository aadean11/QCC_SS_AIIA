<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SsSubmission;
use App\Services\SsScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class KaryawanSsController extends Controller
{
    private function getCurrentEmployee()
    {
        $user = Auth::user();

        return $user ? $user->employee : null;
    }

    private function checkAccess()
    {
        return Auth::check() && session('active_role') === 'employee';
    }

    /**
     * @return array{0: Employee, 1: null}|\Illuminate\Http\RedirectResponse
     */
    private function requireCanCreateSs()
    {
        if (!$this->checkAccess()) {
            return redirect('/login');
        }

        $employee = $this->getCurrentEmployee();
        if (!$employee) {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        if (!$employee->canSubmitOwnSs()) {
            abort(403, 'Hanya LDR, SPV, atau KDP yang dapat mengajukan SS.');
        }

        return [$employee, null];
    }

    private function submissionsQuery(Employee $employee)
    {
        if ($employee->isLdr()) {
            $deptCode = $employee->getDeptCode();

            return SsSubmission::with(['employee', 'ldr'])
                ->when($deptCode, fn ($q) => $q->where('department_code', $deptCode), fn ($q) => $q->whereRaw('1 = 0'));
        }

        return SsSubmission::with(['employee', 'ldr'])
            ->where('employee_npk', $employee->npk);
    }

    private function oprListForDepartment(Employee $ldr)
    {
        $deptCode = $ldr->getDeptCode();
        if (!$deptCode) {
            return collect();
        }

        return Employee::with(['subSection.section'])
            ->occupation('OPR')
            ->inDepartment($deptCode)
            ->orderBy('nama')
            ->get();
    }

    private function findOprInDepartment(Employee $ldr, string $npk): ?Employee
    {
        $deptCode = $ldr->getDeptCode();
        if (!$deptCode) {
            return null;
        }

        return Employee::occupation('OPR')
            ->inDepartment($deptCode)
            ->where('npk', $npk)
            ->first();
    }

    private function storeSubmissionFile(Request $request, string $npkSuffix): string
    {
        if (!$request->hasFile('file')) {
            throw new \Exception('File tidak ditemukan.');
        }

        $file = $request->file('file');
        $fileName = 'SS_' . time() . '_' . $npkSuffix . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('ss_files', $fileName, 'public');

        if (!$path) {
            throw new \Exception('Gagal menyimpan file.');
        }

        return $path;
    }

    private function initialApprovalData(Employee $submitter, ?array $ldrScores = null, ?int $ldrScoreTotal = null): array
    {
        if ($submitter->isKadept()) {
            return [
                'ldr_status' => 'approved',
                'spv_status' => 'approved',
                'status' => 'kdp_review',
            ];
        }

        if ($submitter->isSpv()) {
            return [
                'ldr_status' => 'approved',
                'spv_npk' => $submitter->npk,
                'spv_status' => 'approved',
                'spv_approved_at' => now(),
                'status' => 'kdp_review',
            ];
        }

        return [
            'ldr_npk' => $submitter->npk,
            'ldr_status' => 'approved',
            'ldr_approved_at' => now(),
            'ldr_scores' => $ldrScores,
            'ldr_score_total' => $ldrScoreTotal,
            'score' => $ldrScoreTotal,
            'status' => 'spv_review',
        ];
    }

    public function index(Request $request)
    {
        if (!$this->checkAccess()) {
            return redirect('/login');
        }

        $employee = $this->getCurrentEmployee();
        if (!$employee) {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = $this->submissionsQuery($employee);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('employee', fn ($sq) => $sq->where('nama', 'like', "%{$search}%"));
            });
        }

        $submissions = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $user = $employee;

        return view('ss.karyawan.index', compact('user', 'submissions'));
    }

    public function create()
    {
        $result = $this->requireCanCreateSs();
        if (!is_array($result)) {
            return $result;
        }
        [$employee] = $result;
        $user = $employee;

        $oprList = $employee->isLdr() ? $this->oprListForDepartment($employee) : collect();
        $myDept = $employee->getDepartment();
        $ideaTypes = SsScoringService::ideaTypes();
        $implementationStatuses = SsScoringService::implementationStatuses();
        $criteria = SsScoringService::criteria();
        $criteriaMaxScores = SsScoringService::criteriaMaxScores();

        return view('ss.karyawan.create', compact('user', 'oprList', 'myDept', 'ideaTypes', 'implementationStatuses', 'criteria', 'criteriaMaxScores'));
    }

    public function store(Request $request)
    {
        $result = $this->requireCanCreateSs();
        if (!is_array($result)) {
            return $result;
        }
        [$submitter] = $result;

        $request->validate([
            'submission_type' => 'required|in:opr,self',
            /*
            'idea_title' => 'required|string|max:255',
            'idea_types' => 'required|array|min:1',
            'idea_types.*' => ['string', Rule::in(array_keys(SsScoringService::ideaTypes()))],
            'implemented_date' => 'required|date',
            'idea_location' => 'required|string|max:255',
            'before_condition' => 'required|string',
            'cause' => 'required|string',
            'action' => 'required|string',
            'result' => 'required|string',
            'standardization' => 'nullable|string',
            'benefit' => 'required|string',
            'benefit_amount' => 'nullable|numeric|min:0',
            'implementation_status' => ['required', 'string', Rule::in(array_keys(SsScoringService::implementationStatuses()))],
            */
            'file' => 'required|mimes:pdf|max:5120',
            'notes' => 'nullable|string|max:500',
            'employee_npk' => 'nullable|string',
        ]);

        $ldrScores = null;
        $ldrScoreTotal = null;

        if ($submitter->isLdr()) {
            $request->validate(SsScoringService::requiredScoreValidationRules());
            $ldrScores = SsScoringService::normalizeScores($request->input('scores', []));
            $ldrScoreTotal = SsScoringService::total($ldrScores);
        }

        $departmentCode = $submitter->getDeptCode();
        if (!$departmentCode) {
            return redirect()->back()->withInput()->with('error', 'Departemen tidak terdeteksi.');
        }

        try {
            if ($request->submission_type === 'opr') {
                if (!$submitter->isLdr()) {
                    return redirect()->back()->with('error', 'Hanya Leader (LDR) yang dapat mengajukan SS untuk operator.');
                }

                $request->validate([
                    'employee_npk' => ['required', 'string', Rule::exists('m_employees', 'npk')],
                ]);

                $opr = $this->findOprInDepartment($submitter, $request->employee_npk);
                if (!$opr) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Operator yang dipilih tidak valid atau bukan OPR di departemen Anda.');
                }

                $path = $this->storeSubmissionFile($request, $opr->npk);

                SsSubmission::create(array_merge([
                    'employee_npk' => $opr->npk,
                    'department_code' => $departmentCode,
                    'idea_no' => $this->generateIdeaNo($departmentCode),
                    'idea_title' => $request->idea_title,
                    'idea_types' => $request->idea_types,
                    'file_path' => $path,
                    'submission_date' => now(),
                    'implemented_date' => $request->implemented_date,
                    'idea_location' => $request->idea_location,
                    'notes' => $request->notes,
                    'before_condition' => $request->before_condition,
                    'cause' => $request->cause,
                    'action' => $request->action,
                    'result' => $request->result,
                    'standardization' => $request->standardization,
                    'benefit' => $request->benefit,
                    'benefit_amount' => $request->benefit_amount ?? 0,
                    'implementation_status' => $request->implementation_status ?? 'sudah_dilaksanakan',
                ], $this->initialApprovalData($submitter, $ldrScores, $ldrScoreTotal)));

                $message = 'SS operator berhasil diajukan dan dinilai Leader. Menunggu review dan penilaian SPV.';
            } else {
                $path = $this->storeSubmissionFile($request, $submitter->npk);

                SsSubmission::create(array_merge([
                    'employee_npk' => $submitter->npk,
                    'department_code' => $departmentCode,
                    'idea_no' => $this->generateIdeaNo($departmentCode),
                    'idea_title' => $request->idea_title,
                    'idea_types' => $request->idea_types,
                    'file_path' => $path,
                    'submission_date' => now(),
                    'implemented_date' => $request->implemented_date,
                    'idea_location' => $request->idea_location,
                    'notes' => $request->notes,
                    'before_condition' => $request->before_condition,
                    'cause' => $request->cause,
                    'action' => $request->action,
                    'result' => $request->result,
                    'standardization' => $request->standardization,
                    'benefit' => $request->benefit,
                    'benefit_amount' => $request->benefit_amount ?? 0,
                    'implementation_status' => $request->implementation_status ?? 'sudah_dilaksanakan',
                ], $this->initialApprovalData($submitter, $ldrScores, $ldrScoreTotal)));

                $message = match (true) {
                    $submitter->isKadept() => 'SS pribadi berhasil diajukan. Menunggu review KDP.',
                    $submitter->isSpv() => 'SS pribadi berhasil diajukan. Menunggu review KDP.',
                    default => 'SS pribadi berhasil diajukan dan dinilai Leader. Menunggu review SPV.',
                };
            }

            return redirect()->route('ss.karyawan.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        if (!$this->checkAccess()) {
            return redirect('/login');
        }

        $employee = $this->getCurrentEmployee();
        if (!$employee) {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $submission = $this->submissionsQuery($employee)
            ->where('id', $id)
            ->firstOrFail();

        $user = $employee;

        return view('ss.karyawan.show', compact('user', 'submission'));
    }

    private function generateIdeaNo(string $departmentCode): string
    {
        $prefix = 'SS-' . now()->format('Ym') . '-' . strtoupper($departmentCode);
        $next = SsSubmission::where('idea_no', 'like', $prefix . '-%')->count() + 1;

        return $prefix . '-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}

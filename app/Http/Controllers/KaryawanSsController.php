<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SsSubmission;
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

        return view('ss.karyawan.create', compact('user', 'oprList', 'myDept'));
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
            'score' => 'required|numeric|min:0',
            'file' => 'required|mimes:pdf|max:5120',
            'notes' => 'nullable|string|max:500',
            'employee_npk' => 'nullable|string',
        ]);

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

                SsSubmission::create([
                    'employee_npk' => $opr->npk,
                    'department_code' => $departmentCode,
                    'file_path' => $path,
                    'submission_date' => now(),
                    'score' => $request->score,
                    'notes' => $request->notes,
                    'ldr_npk' => $submitter->npk,
                    'status' => 'assessed',
                ]);

                $message = 'SS operator berhasil diajukan dengan nilai. Menunggu review SPV.';
            } else {
                $path = $this->storeSubmissionFile($request, $submitter->npk);

                SsSubmission::create([
                    'employee_npk' => $submitter->npk,
                    'department_code' => $departmentCode,
                    'file_path' => $path,
                    'submission_date' => now(),
                    'score' => $request->score,
                    'notes' => $request->notes,
                    'ldr_npk' => $submitter->isLdr() ? $submitter->npk : null,
                    'status' => 'assessed',
                ]);

                $message = 'SS pribadi berhasil diajukan. Menunggu review SPV.';
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
}

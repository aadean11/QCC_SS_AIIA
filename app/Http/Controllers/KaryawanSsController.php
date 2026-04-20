<?php

namespace App\Http\Controllers;

use App\Models\SsSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function index()
    {
        if (!$this->checkAccess()) return redirect('/login');
        $employee = $this->getCurrentEmployee();
        if (!$employee) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $submissions = SsSubmission::where('employee_npk', $employee->npk)
            ->orderBy('created_at', 'desc')
            ->get();

        // Kirim sebagai $user agar layout welcome.blade.php bisa mengakses
        $user = $employee;

        return view('ss.karyawan.index', compact('user', 'submissions'));
    }

    public function create()
    {
        if (!$this->checkAccess()) return redirect('/login');
        $user = $this->getCurrentEmployee();

        return view('ss.karyawan.create', compact('user'));
    }

    public function store(Request $request)
    {
        if (!$this->checkAccess()) return redirect('/login');

        $request->validate([
            'file' => 'required|mimes:pdf|max:5120',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $employee = $this->getCurrentEmployee();
            if (!$employee) throw new \Exception('Data karyawan tidak ditemukan.');

            $department = $employee->getDepartment();
            $departmentCode = $department ? $department->code : null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $folderPath = "ss_files";
                $fileName = 'SS_' . time() . '_' . $employee->npk . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($folderPath, $fileName, 'public');

                if (!$path) {
                    throw new \Exception('Gagal menyimpan file.');
                }

                SsSubmission::create([
                    'employee_npk' => $employee->npk,
                    'department_code' => $departmentCode,
                    'file_path' => $path,
                    'submission_date' => now(),
                    'notes' => $request->notes,
                    'status' => 'submitted',
                ]);

                return redirect()->route('ss.karyawan.index')->with('success', 'SS berhasil diupload.');
            } else {
                throw new \Exception('File tidak ditemukan.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        if (!$this->checkAccess()) return redirect('/login');
        $employee = $this->getCurrentEmployee();
        if (!$employee) return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');

        $submission = SsSubmission::where('employee_npk', $employee->npk)
            ->where('id', $id)
            ->firstOrFail();

        $user = $employee;

        return view('ss.karyawan.show', compact('user', 'submission'));
    }
}
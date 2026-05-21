<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Occupation;
use App\Models\SubSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        // Ambil user yang sedang login (dari tabel users)
        $user = Auth::user();
        
        // Jika tidak login, redirect (seharusnya sudah di middleware, tapi amankan)
        if (!$user) {
            return redirect('/login');
        }

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        // Query karyawan dengan relasi
        $employees = Employee::with(['job', 'subSection.section.department'])
            ->when($search, function($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('npk', 'like', "%{$search}%");
            })
            ->orderBy('npk', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $occupations = Occupation::all();
        $subSections = SubSection::all();

        return view('admin.employee', compact('user', 'employees', 'perPage', 'occupations', 'subSections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        Employee::create($this->employeePayload($validated, true));
        return redirect()->back()->with('success', 'Data karyawan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $emp = Employee::findOrFail($id);
        $validated = $request->validate($this->rules($emp->id), $this->messages());

        $emp->update($this->employeePayload($validated));
        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Employee::destroy($id);
        return redirect()->back()->with('success', 'Data karyawan telah dihapus dari sistem.');
    }

    private function rules(?int $employeeId = null): array
    {
        return [
            'npk' => [
                'required',
                'string',
                'max:6',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('m_employees', 'npk')->ignore($employeeId),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'regex:/^[0-9+().\s-]+$/'],
            'line_code' => ['required', 'string', 'max:255'],
            'sub_section' => ['required', 'string', Rule::exists('m_sub_sections', 'code')],
            'occupation' => ['required', 'string', Rule::exists('m_occupations', 'code')],
            'transport' => ['required', 'string', 'max:255'],
            'status_emp' => ['required', 'string', 'max:255'],
            'employment_status' => ['required', 'string', 'max:255'],
        ];
    }

    private function messages(): array
    {
        return [
            'npk.required' => 'NPK wajib diisi.',
            'npk.unique' => 'NPK sudah terdaftar.',
            'npk.regex' => 'NPK hanya boleh berisi huruf, angka, titik, underscore, dan strip.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'line_code.required' => 'Dept / Line Code wajib diisi.',
            'sub_section.required' => 'Sub-Section wajib dipilih.',
            'sub_section.exists' => 'Sub-Section yang dipilih tidak valid.',
            'occupation.required' => 'Jabatan wajib dipilih.',
            'occupation.exists' => 'Jabatan yang dipilih tidak valid.',
            'phone.required' => 'Nomor WA wajib diisi.',
            'phone.regex' => 'Nomor WA hanya boleh berisi angka dan simbol nomor telepon.',
            'transport.required' => 'Transport wajib diisi.',
            'status_emp.required' => 'Status karyawan wajib diisi.',
            'employment_status.required' => 'Status employment wajib diisi.',
        ];
    }

    private function employeePayload(array $validated, bool $withDefaults = false): array
    {
        if (!$withDefaults) {
            return $validated;
        }

        for ($month = 1; $month <= 12; $month++) {
            $validated["quota_used_{$month}"] = 0;
            $validated["quota_remain_{$month}"] = 0;
        }

        return $validated;
    }
}

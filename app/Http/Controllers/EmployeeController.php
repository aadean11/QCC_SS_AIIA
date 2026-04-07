<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Occupation;
use App\Models\SubSection;
use Illuminate\Support\Facades\Auth;

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

        // Generate NPK otomatis (urutan numerik dari NPK yang dimulai dengan '0')
        $lastEmployee = Employee::where('npk', 'like', '0%')
            ->orderByRaw('CAST(npk AS UNSIGNED) DESC')
            ->first();

        if ($lastEmployee) {
            $currentMax = (int)$lastEmployee->npk;
            $nextNpk = str_pad($currentMax + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $nextNpk = '000001';
        }

        $occupations = Occupation::all();
        $subSections = SubSection::all();

        return view('admin.employee', compact('user', 'employees', 'perPage', 'occupations', 'subSections', 'nextNpk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'npk' => 'required|unique:m_employees,npk',
            'nama' => 'required|string|max:255',
            'line_code' => 'required',
            'sub_section' => 'required',
            'occupation' => 'required',
        ]);

        Employee::create($request->all());
        return redirect()->back()->with('success', 'Data karyawan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $emp = Employee::findOrFail($id);
        $request->validate([
            'npk' => 'required|unique:m_employees,npk,' . $id,
            'nama' => 'required',
        ]);

        $emp->update($request->all());
        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Employee::destroy($id);
        return redirect()->back()->with('success', 'Data karyawan telah dihapus dari sistem.');
    }
}
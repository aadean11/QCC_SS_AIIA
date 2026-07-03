<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\QccCircle;
use App\Models\QccTarget;
use App\Models\Section;
use App\Models\SsSubmission;
use App\Models\SsTarget;
use App\Models\SubSection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MasterOrgController extends Controller
{
    // ==================== DEPARTMENTS ====================

    public function departmentsIndex(Request $request)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $perPage = (int) $request->get('per_page', 10);
        $search = $request->get('search');

        $departments = Department::with('division')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('alias', 'like', "%{$search}%")
                        ->orWhere('npk', 'like', "%{$search}%");
                });
            })
            ->orderBy('code')
            ->paginate($perPage)
            ->withQueryString();

        $divisions = Division::orderBy('code')->get();

        $user = $this->viewUser();
        if (!$user) {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        return view('admin.departments', compact('departments', 'perPage', 'divisions', 'user'));
    }

    public function storeDepartment(Request $request)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        try {
            $validated = $request->validate($this->departmentRules(), $this->departmentMessages());
            Department::create($validated);
            return redirect()->back()->with('success', 'Departemen berhasil ditambahkan!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan departemen: ' . $this->getFriendlyDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateDepartment(Request $request, $id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        try {
            $department = Department::findOrFail($id);
            $validated = $request->validate($this->departmentRules($department->id), $this->departmentMessages());

            $oldCode = $department->code;
            $newCode = $validated['code'];

            DB::transaction(function () use ($department, $validated, $oldCode, $newCode) {
                if ($oldCode !== $newCode) {
                    Section::where('code_department', $oldCode)->update(['code_department' => $newCode]);
                    QccTarget::where('department_code', $oldCode)->update(['department_code' => $newCode]);
                    QccCircle::where('department_code', $oldCode)->update(['department_code' => $newCode]);
                    SsSubmission::where('department_code', $oldCode)->update(['department_code' => $newCode]);
                    SsTarget::where('department_code', $oldCode)->update(['department_code' => $newCode]);
                }
                $department->update($validated);
            });

            return redirect()->back()->with('success', 'Departemen berhasil diperbarui!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui departemen: ' . $this->getFriendlyDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroyDepartment($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        try {
            $department = Department::findOrFail($id);

            if ($department->sections()->exists()) {
                return redirect()->back()->with('error', 'Departemen tidak bisa dihapus karena masih memiliki section.');
            }
            if ($department->ssTargets()->exists()) {
                return redirect()->back()->with('error', 'Departemen tidak bisa dihapus karena masih digunakan pada target SS.');
            }
            if (QccTarget::where('department_code', $department->code)->exists()) {
                return redirect()->back()->with('error', 'Departemen tidak bisa dihapus karena masih digunakan pada target QCC.');
            }
            if (QccCircle::where('department_code', $department->code)->exists()) {
                return redirect()->back()->with('error', 'Departemen tidak bisa dihapus karena masih digunakan pada data circle QCC.');
            }
            if (SsSubmission::where('department_code', $department->code)->exists()) {
                return redirect()->back()->with('error', 'Departemen tidak bisa dihapus karena masih digunakan pada data SS.');
            }

            $department->delete();
            return redirect()->back()->with('success', 'Departemen berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Gagal menghapus departemen: ' . $this->getFriendlyDbError($e));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ==================== SECTIONS ====================

    public function sectionsIndex(Request $request)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $perPage = (int) $request->get('per_page', 10);
        $search = $request->get('search');

        $sections = Section::with('department')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('alias', 'like', "%{$search}%")
                        ->orWhere('code_department', 'like', "%{$search}%")
                        ->orWhere('npk', 'like', "%{$search}%");
                });
            })
            ->orderBy('code')
            ->paginate($perPage)
            ->withQueryString();

        $departments = Department::orderBy('code')->get();

        $user = $this->viewUser();
        if (!$user) {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        return view('admin.sections', compact('sections', 'perPage', 'departments', 'user'));
    }

    public function storeSection(Request $request)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        try {
            $validated = $request->validate($this->sectionRules(), $this->sectionMessages());
            Section::create($validated);
            return redirect()->back()->with('success', 'Section berhasil ditambahkan!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan section: ' . $this->getFriendlyDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateSection(Request $request, $id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        try {
            $section = Section::findOrFail($id);
            $validated = $request->validate($this->sectionRules($section->id), $this->sectionMessages());

            $oldCode = $section->code;
            $newCode = $validated['code'];

            DB::transaction(function () use ($section, $validated, $oldCode, $newCode) {
                if ($oldCode !== $newCode) {
                    SubSection::where('code_section', $oldCode)->update(['code_section' => $newCode]);
                    Employee::where('sub_section', $oldCode)->update(['sub_section' => $newCode]);
                }
                $section->update($validated);
            });

            return redirect()->back()->with('success', 'Section berhasil diperbarui!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui section: ' . $this->getFriendlyDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroySection($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        try {
            $section = Section::findOrFail($id);

            if ($section->subSections()->exists()) {
                return redirect()->back()->with('error', 'Section tidak bisa dihapus karena masih memiliki sub section.');
            }
            if (Employee::where('sub_section', $section->code)->exists()) {
                return redirect()->back()->with('error', 'Section tidak bisa dihapus karena masih digunakan pada data karyawan.');
            }

            $section->delete();
            return redirect()->back()->with('success', 'Section berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Gagal menghapus section: ' . $this->getFriendlyDbError($e));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ==================== SUB SECTIONS ====================

    public function subSectionsIndex(Request $request)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $perPage = (int) $request->get('per_page', 10);
        $search = $request->get('search');

        $subSections = SubSection::with('section.department')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('alias', 'like', "%{$search}%")
                        ->orWhere('code_section', 'like', "%{$search}%")
                        ->orWhere('npk', 'like', "%{$search}%");
                });
            })
            ->orderBy('code')
            ->paginate($perPage)
            ->withQueryString();

        $sections = Section::with('department')->orderBy('code')->get();

        $user = $this->viewUser();
        if (!$user) {
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        return view('admin.sub_sections', compact('subSections', 'perPage', 'sections', 'user'));
    }

    public function storeSubSection(Request $request)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        try {
            $validated = $request->validate($this->subSectionRules(), $this->subSectionMessages());
            SubSection::create($validated);
            return redirect()->back()->with('success', 'Sub section berhasil ditambahkan!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan sub section: ' . $this->getFriendlyDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateSubSection(Request $request, $id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        try {
            $subSection = SubSection::findOrFail($id);
            $validated = $request->validate($this->subSectionRules($subSection->id), $this->subSectionMessages());

            $oldCode = $subSection->code;
            $newCode = $validated['code'];

            DB::transaction(function () use ($subSection, $validated, $oldCode, $newCode) {
                if ($oldCode !== $newCode) {
                    Employee::where('sub_section', $oldCode)->update(['sub_section' => $newCode]);
                }
                $subSection->update($validated);
            });

            return redirect()->back()->with('success', 'Sub section berhasil diperbarui!');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui sub section: ' . $this->getFriendlyDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroySubSection($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        try {
            $subSection = SubSection::findOrFail($id);

            if (Employee::where('sub_section', $subSection->code)->exists()) {
                return redirect()->back()->with('error', 'Sub section tidak bisa dihapus karena masih digunakan pada data karyawan.');
            }

            $subSection->delete();
            return redirect()->back()->with('success', 'Sub section berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Gagal menghapus sub section: ' . $this->getFriendlyDbError($e));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ==================== HELPERS ====================

    private function ensureAdmin()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return redirect('/login')->with('error', 'Akses ditolak.');
        }
        return null;
    }

    private function viewUser()
    {
        $authUser = Auth::user();
        if (!$authUser) {
            return null;
        }
        return $authUser->employee ?? Employee::where('npk', $authUser->npk)->first();
    }

    private function getFriendlyDbError(QueryException $e): string
    {
        $code = $e->errorInfo[1] ?? 0;
        if ($code === 1451 || $code === 1452) {
            return 'Data masih digunakan oleh relasi lain, tidak dapat dihapus/diubah.';
        }
        if ($code === 1062) {
            return 'Kode yang dimasukkan sudah digunakan, silakan gunakan kode lain.';
        }
        return 'Terjadi kesalahan database. Silakan coba lagi.';
    }

    // ==================== VALIDATION RULES (SEMUA REQUIRED) ====================

    private function departmentRules(?int $id = null): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('m_departments', 'code')->ignore($id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'alias' => ['required', 'string', 'max:255'],
            'code_division' => ['required', 'string', Rule::exists('m_divisions', 'code')],
            'npk' => ['required', 'string', 'max:6', 'regex:/^[A-Za-z0-9._-]+$/'],
        ];
    }

    private function departmentMessages(): array
    {
        return [
            'code.required' => 'Kode departemen wajib diisi.',
            'code.unique' => 'Kode departemen sudah digunakan.',
            'code.regex' => 'Kode departemen hanya boleh berisi huruf, angka, titik, underscore, dan strip.',
            'name.required' => 'Nama departemen wajib diisi.',
            'alias.required' => 'Alias departemen wajib diisi.',
            'code_division.required' => 'Division wajib dipilih.',
            'code_division.exists' => 'Division yang dipilih tidak valid.',
            'npk.required' => 'NPK Ka. Departemen wajib diisi.',
            'npk.regex' => 'Format NPK tidak valid.',
        ];
    }

    private function sectionRules(?int $id = null): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('m_sections', 'code')->ignore($id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'alias' => ['required', 'string', 'max:255'],
            'code_department' => ['required', 'string', Rule::exists('m_departments', 'code')],
            'npk' => ['required', 'string', 'max:6', 'regex:/^[A-Za-z0-9._-]+$/'],
            'npk_admin' => ['required', 'string', 'max:6', 'regex:/^[A-Za-z0-9._-]+$/'],
        ];
    }

    private function sectionMessages(): array
    {
        return [
            'code.required' => 'Kode section wajib diisi.',
            'code.unique' => 'Kode section sudah digunakan.',
            'code.regex' => 'Kode section hanya boleh berisi huruf, angka, titik, underscore, dan strip.',
            'name.required' => 'Nama section wajib diisi.',
            'alias.required' => 'Alias section wajib diisi.',
            'code_department.required' => 'Departemen wajib dipilih.',
            'code_department.exists' => 'Departemen yang dipilih tidak valid.',
            'npk.required' => 'NPK Ka. Section wajib diisi.',
            'npk.regex' => 'Format NPK tidak valid.',
            'npk_admin.required' => 'NPK Admin Section wajib diisi.',
            'npk_admin.regex' => 'Format NPK Admin tidak valid.',
        ];
    }

    private function subSectionRules(?int $id = null): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('m_sub_sections', 'code')->ignore($id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'alias' => ['required', 'string', 'max:255'],
            'code_section' => ['required', 'string', Rule::exists('m_sections', 'code')],
            'npk' => ['required', 'string', 'max:6', 'regex:/^[A-Za-z0-9._-]+$/'],
        ];
    }

    private function subSectionMessages(): array
    {
        return [
            'code.required' => 'Kode sub section wajib diisi.',
            'code.unique' => 'Kode sub section sudah digunakan.',
            'code.regex' => 'Kode sub section hanya boleh berisi huruf, angka, titik, underscore, dan strip.',
            'name.required' => 'Nama sub section wajib diisi.',
            'alias.required' => 'Alias sub section wajib diisi.',
            'code_section.required' => 'Section wajib dipilih.',
            'code_section.exists' => 'Section yang dipilih tidak valid.',
            'npk.required' => 'NPK Ka. Sub Section wajib diisi.',
            'npk.regex' => 'Format NPK tidak valid.',
        ];
    }
}
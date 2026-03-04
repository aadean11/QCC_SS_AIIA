<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Occupation;
use App\Models\Role;
use App\Models\SubSection;
use App\Models\Section; // Pastikan di-import
use App\Models\Department;

class Employee extends Authenticatable
{
    use Notifiable;

    protected $table = 'm_employees';

    protected $fillable = [
        'nama', 'npk', 'line_code', 'sub_section', 'occupation',
    ];

    public function job()
    {
        return $this->belongsTo(Occupation::class, 'occupation', 'code');
    }

    // --- RELASI BARU: LANGSUNG KE SECTION ---
    public function section()
    {
        // Mengasumsikan line_code di m_employees merujuk ke code di m_sections
        return $this->belongsTo(Section::class, 'sub_section', 'code');
    }

    public function subSection()
    {
        return $this->belongsTo(SubSection::class, 'sub_section', 'code');
    }

    public function getDeptCode()
    {
        // 1. Jika dia Kepala Departemen (KDP), cek langsung ke tabel m_departments
        if ($this->occupation === 'KDP') {
            $managedDept = \App\Models\Department::where('npk', $this->npk)->first();
            if ($managedDept) {
                return $managedDept->code;
            }
        }

        // 2. Jalur Hirarki: SubSection -> Section -> Department
        if ($this->subSection && $this->subSection->section) {
            return $this->subSection->section->code_department;
        }

        // 3. Jalur Hirarki: Section -> Department
        if ($this->section) {
            return $this->section->code_department;
        }

        // 4. Fallback ke line_code jika ada
        return $this->line_code;
    }

    public function getDepartment()
    {
        $deptCode = $this->getDeptCode();
        if ($deptCode) {
            return \App\Models\Department::where('code', $deptCode)->first();
        }
        return null;
    }

    // Helper tambahan untuk mengecek akses
    public function isSpv() { return $this->occupation === 'SPV'; }
    public function isKadept() { return $this->occupation === 'KDP'; }

    /**
     * Scope untuk memfilter karyawan berdasarkan kode departemen
    */
    public function scopeInDepartment($query, $deptCode)
    {
        return $query->where(function($q) use ($deptCode) {
            // JALUR 1 & 2: Untuk Staff/SPV (Lewat Hirarki Section)
            $q->whereHas('subSection.section', function($sq) use ($deptCode) {
                $sq->where('code_department', $deptCode);
            })
            ->orWhereHas('section', function($sq) use ($deptCode) {
                $sq->where('code_department', $deptCode);
            })
            
            // JALUR 3: KHUSUS KDP (Mencocokkan NPK di tabel m_departments)
            // Jika NPK karyawan ini terdaftar sebagai penanggung jawab Dept tersebut
            ->orWhereIn('npk', function($sq) use ($deptCode) {
                $sq->select('npk')
                ->from('m_departments')
                ->where('code', $deptCode);
            });
        });
    }
}
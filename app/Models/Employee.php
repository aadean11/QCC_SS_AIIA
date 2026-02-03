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
        // 1. LOGIKA KDP (Kepala Departemen)
        if ($this->occupation === 'KDP') {
            $dept = Department::where('npk', $this->npk)->first();
            if ($dept) return $dept->code;
        }

        // 2. LOGIKA SPV / STAFF (Berdasarkan Hirarki)
        
        // Jalur A: Lewat Sub Section (Hirarki Terendah)
        // Employee -> m_sub_sections -> m_sections -> code_department
        $sub = $this->subSection; 
        if ($sub && $sub->section) {
            return $sub->section->code_department;
        }

        // Jalur B: Lewat Section Langsung (Jika Sub Section kosong/gagal)
        // Employee -> m_sections -> code_department
        $sec = $this->section;
        if ($sec) {
            return $sec->code_department;
        }

        // 3. FALLBACK (Terakhir)
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
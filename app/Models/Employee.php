<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Occupation;
use App\Models\Role;
use App\Models\SubSection;

class Employee extends Authenticatable
{
    use Notifiable;

    protected $table = 'm_employees';

    protected $fillable = [
        'nama', 'npk', 'line_code', 'sub_section', 'occupation',
    ];

    public function getAuthPassword()
    {
        return $this->npk;
    }

    // RELASI KE TABEL JABATAN
    // Parameter: (NamaModel, foreign_key_di_employee, primary_key_di_occupation)
    public function job()
    {
        return $this->belongsTo(Occupation::class, 'occupation', 'code');
    }

    public function isAdmin()
    {
        // 1. Cek apakah saat login dia memilih tombol 'Admin'
        if (session('login_as') !== 'admin') {
            return false;
        }

        // 2. Pastikan NPK-nya memang terdaftar di tabel roles
        return \App\Models\Role::where('npk', $this->npk)
                            ->where('display_name', 'Admin')
                            ->exists();
    }

    public function isSpv()
    {
        // Menggunakan kode 'SPV' sesuai tabel m_occupations yang Anda berikan sebelumnya
        return $this->occupation === 'SPV'; 
    }

    public function isKadept()
    {
        // Menggunakan kode 'KDP' untuk Kepala Departement
        return $this->occupation === 'KDP';
    }

    public function subSection()
    {
        // Relasi ke tabel m_sub_sections
        return $this->belongsTo(SubSection::class, 'sub_section', 'code');
    }

    public function getDeptCode()
    {
        // 1. Ambil objek SubSection (misal: PP31)
        $sub = $this->subSection;
        
        // 2. Ambil objek Section dari SubSection (misal: PP3)
        $section = $sub ? $sub->section : null;
        
        // 3. Ambil kode Department dari Section (misal: PPC)
        if ($section) {
            return $section->code_department;
        }

        // Jika semua gagal, gunakan line_code sebagai cadangan terakhir
        return $this->line_code;
    }
}
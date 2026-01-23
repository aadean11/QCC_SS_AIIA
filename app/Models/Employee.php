<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Occupation;
use App\Models\Role;

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
}
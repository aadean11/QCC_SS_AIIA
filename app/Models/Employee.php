<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use Notifiable;

    protected $table = 'm_employees';

    // Karena tabel m_employees menggunakan id, npk, nama
    protected $fillable = [
        'nama', 'npk', 'line_code', 'sub_section'
    ];

    // Jika Anda ingin menggunakan Auth bawaan laravel, 
    // Beritahu laravel bahwa 'password' sebenarnya ada di kolom 'npk'
    public function getAuthPassword()
    {
        return $this->npk;
    }
}
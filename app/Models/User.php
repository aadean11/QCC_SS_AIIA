<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'npk', 'nama', 'email', 'password', 'role', 'status_user', 'ot_par', 'limit_mp'
    ];

    // Relasi ke tabel m_employees berdasarkan NPK
    public function employee()
    {
        return $this->hasOne(Employee::class, 'npk', 'npk');
    }

    public function getJobAttribute()
    {
        return (object) ['name' => $this->role];
    }

    /**
     * Cek apakah user saat login sebagai admin.
     * Mengecek dari tabel Role ATAU kolom 'role' di users.
     */
    public function isAdmin()
    {
        // Cek dari tabel Role
        if (Role::where('npk', $this->npk)->where('display_name', 'Admin')->exists()) {
            return true;
        }
        // Cek dari kolom role users
        return strtolower($this->role) === 'admin';
    }
}

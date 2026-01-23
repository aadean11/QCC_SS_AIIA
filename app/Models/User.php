<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    // Beritahu Laravel bahwa "password" sebenarnya ada di kolom "npk"
    public function getAuthPassword()
    {
        return $this->npk;
    }

    // Fungsi bantuan agar di Blade {{ $user->job->name }} tidak error.
    // Karena di tabel users tidak ada 'occupation', kita gunakan kolom 'role' sebagai jabatannya.
    public function getJobAttribute()
    {
        return (object) ['name' => $this->role];
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

    // /** @use HasFactory<\Database\Factories\UserFactory> */
    // use HasFactory, Notifiable;

    // /**
    //  * The attributes that are mass assignable.
    //  *
    //  * @var list<string>
    //  */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    // /**
    //  * The attributes that should be hidden for serialization.
    //  *
    //  * @var list<string>
    //  */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    // /**
    //  * Get the attributes that should be cast.
    //  *
    //  * @return array<string, string>
    //  */
    // protected function casts(): array
    // {
    //     return [
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //     ];
    // }
}

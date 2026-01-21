<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'm_divisions';

    protected $fillable = [
        'code', 'name', 'npk', 'code_director'
    ];

    // Relasi ke Department
    public function departments()
    {
        return $this->hasMany(Department::class, 'code_division', 'code');
    }
}
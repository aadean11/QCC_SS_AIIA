<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'm_departments';

    protected $fillable = [
        'code', 'name', 'alias', 'code_division', 'npk'
    ];

    // Relasi balik ke Division
    public function division()
    {
        return $this->belongsTo(Division::class, 'code_division', 'code');
    }

    // Relasi ke Sections
    public function sections()
    {
        return $this->hasMany(Section::class, 'code_department', 'code');
    }
}
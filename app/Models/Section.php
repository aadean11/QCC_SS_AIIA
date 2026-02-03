<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'm_sections';

    protected $fillable = [
        'code', 'name', 'alias', 'code_department', 'npk', 'npk_admin'
    ];

    public function department()
    {
        // Menghubungkan m_sections.code_department ke m_departments.code
        return $this->belongsTo(Department::class, 'code_department', 'code');
    }

    // Relasi ke Sub Sections
    public function subSections()
    {
        return $this->hasMany(SubSection::class, 'code_section', 'code');
    }
}
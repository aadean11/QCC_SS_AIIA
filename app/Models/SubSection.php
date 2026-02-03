<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSection extends Model
{
    protected $table = 'm_sub_sections';

    protected $fillable = [
        'code', 'name', 'alias', 'code_section', 'npk'
    ];

    public function section()
    {
        // Menghubungkan m_sub_sections.code_section ke m_sections.code
        return $this->belongsTo(Section::class, 'code_section', 'code');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSection extends Model
{
    protected $table = 'm_sub_sections';

    protected $fillable = [
        'code', 'name', 'alias', 'code_section', 'npk'
    ];

    // Relasi balik ke Section
    public function section()
    {
        return $this->belongsTo(Section::class, 'code_section', 'code');
    }
}
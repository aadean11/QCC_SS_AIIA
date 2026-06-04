<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsTarget extends Model
{
    protected $table = 'm_ss_targets';

    protected $fillable = [
        'year',
        'month',
        'department_code',
        'target_amount',
        'description',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'target_amount' => 'integer',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }
}

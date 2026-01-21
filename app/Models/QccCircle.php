<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QccCircle extends Model
{
    protected $table = 'm_qcc_circles';

    protected $fillable = [
        'circle_code', 'circle_name', 'theme', 'department_code', 'qcc_period_id', 'status'
    ];

    public function period()
    {
        return $this->belongsTo(QccPeriod::class, 'qcc_period_id');
    }

    public function department()
    {
        // Berasumsi Anda punya model Department dengan FK 'code'
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    public function members()
    {
        return $this->hasMany(QccCircleMember::class, 'qcc_circle_id');
    }

    public function stepProgress()
    {
        return $this->hasMany(QccCircleStepTransaction::class, 'qcc_circle_id');
    }
}
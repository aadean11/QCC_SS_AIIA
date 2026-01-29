<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QccTarget extends Model
{
    protected $table = 'm_qcc_targets';
    protected $fillable = ['qcc_period_id', 'department_code', 'target_amount', 'description'];

    public function period()
    {
        return $this->belongsTo(QccPeriod::class, 'qcc_period_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }
}
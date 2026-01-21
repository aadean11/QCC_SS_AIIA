<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QccPeriodStep extends Model
{
    protected $table = 'm_qcc_period_steps';

    protected $fillable = [
        'qcc_period_id', 'qcc_step_id', 'deadline_date'
    ];

    public function period()
    {
        return $this->belongsTo(QccPeriod::class, 'qcc_period_id');
    }

    public function step()
    {
        return $this->belongsTo(QccStep::class, 'qcc_step_id');
    }
}
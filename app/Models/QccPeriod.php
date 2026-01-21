<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QccPeriod extends Model
{
    protected $table = 'm_qcc_periods';

    protected $fillable = [
        'period_code', 'period_name', 'year', 'start_date', 'end_date', 'status'
    ];

    // Relasi ke tabel pivot m_qcc_period_steps
    public function periodSteps()
    {
        return $this->hasMany(QccPeriodStep::class, 'qcc_period_id');
    }

    // Relasi ke tabel m_qcc_circles
    public function circles()
    {
        return $this->hasMany(QccCircle::class, 'qcc_period_id');
    }
}
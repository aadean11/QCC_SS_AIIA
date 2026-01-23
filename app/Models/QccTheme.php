<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QccTheme extends Model
{
    protected $table = 'm_qcc_themes';
    protected $fillable = ['qcc_circle_id', 'qcc_period_id', 'theme_name', 'status'];

    public function circle()
    {
        return $this->belongsTo(QccCircle::class, 'qcc_circle_id');
    }

    public function period()
    {
        return $this->belongsTo(QccPeriod::class, 'qcc_period_id');
    }

    public function stepTransactions()
    {
        return $this->hasMany(QccCircleStepTransaction::class, 'qcc_theme_id');
    }
}
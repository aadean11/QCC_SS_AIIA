<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\QccPeriod;
use App\Models\Department;
use App\Models\QccCircleMember;
use App\Models\QccCircleStepTransaction;
use App\Models\QccTheme;

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
        // Menghubungkan department_code di m_qcc_circles ke code di m_departments
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

    public function themes()
    {
        return $this->hasMany(QccTheme::class, 'qcc_circle_id');
    }

    // Helper untuk mengambil tema yang sedang aktif saat ini
    public function activeTheme()
    {
        return $this->hasOne(QccTheme::class, 'qcc_circle_id')->where('status', 'ACTIVE');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QccCircleMember extends Model
{
    protected $table = 'm_qcc_circle_members';

    protected $fillable = [
        'qcc_circle_id', 'employee_npk', 'role', 'is_active', 'joined_at'
    ];

    public function circle()
    {
        return $this->belongsTo(QccCircle::class, 'qcc_circle_id');
    }

    public function employee()
    {
        // Relasi ke Employee menggunakan NPK
        return $this->belongsTo(Employee::class, 'employee_npk', 'npk');
    }
}
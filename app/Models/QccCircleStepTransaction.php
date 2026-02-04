<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QccCircleStepTransaction extends Model
{
    protected $table = 't_qcc_circle_steps';

    protected $fillable = [
        'qcc_circle_id', 
        'qcc_theme_id',
        'qcc_step_id', 
        'file_name', 
        'file_path', 
        'file_type', 
        'upload_by', 
        'status',
        'spv_note',
        'kdp_note'
    ];

    public function circle()
    {
        return $this->belongsTo(QccCircle::class, 'qcc_circle_id');
    }

    public function step()
    {
        return $this->belongsTo(QccStep::class, 'qcc_step_id');
    }

    public function uploader()
    {
        return $this->belongsTo(Employee::class, 'upload_by', 'npk');
    }
}
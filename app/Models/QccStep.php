<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QccStep extends Model
{
    protected $table = 'm_qcc_steps';

    protected $fillable = [
        'step_number', 
        'step_name', 
        'description',
        'template_file_name',
        'template_file_path',
    ];

    // Relasi ke transaksi t_qcc_circle_steps
    public function circleTransactions()
    {
        return $this->hasMany(QccCircleStepTransaction::class, 'qcc_step_id');
    }
}
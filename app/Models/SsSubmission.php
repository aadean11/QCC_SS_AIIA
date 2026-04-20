<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SsSubmission extends Model
{
    use HasFactory;

    protected $table = 'ss_submissions';
    public $timestamps = true;

    protected $fillable = [
        'employee_npk',
        'department_code',
        'file_path',
        'submission_date',
        'score',
        'notes',
        'spv_npk',
        'spv_notes',
        'spv_approved_at',
        'spv_status',
        'kdp_npk',
        'kdp_notes',
        'kdp_approved_at',
        'kdp_status',
        'status',
        'final_approved_at',
        'reward_amount',
        'paid_at',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'spv_approved_at' => 'datetime',
        'kdp_approved_at' => 'datetime',
        'final_approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'score' => 'integer',
        'reward_amount' => 'decimal:0',
    ];

    /**
     * Relasi ke pengaju ide (Employee).
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_npk', 'npk');
    }

    /**
     * Relasi ke departemen
     * Pastikan tabel m_departments memiliki kolom 'code' sebagai primary key atau unique.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    /**
     * Relasi ke SPV yang mereview.
     */
    public function spv()
    {
        return $this->belongsTo(Employee::class, 'spv_npk', 'npk');
    }

    /**
     * Relasi ke KDP yang mereview.
     */
    public function kdp()
    {
        return $this->belongsTo(Employee::class, 'kdp_npk', 'npk');
    }

    /**
     * Scope untuk filter berdasarkan status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk ide yang sudah approved (final).
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope untuk ide yang sudah mendapat reward.
     */
    public function scopeRewarded($query)
    {
        return $query->where('status', 'rewarded');
    }

    /**
     * Cek apakah ide sudah diberi reward.
     */
    public function isRewarded(): bool
    {
        return $this->status === 'rewarded' && !is_null($this->paid_at);
    }

    /**
     * Cek apakah ide sudah final approved.
     */
    public function isApproved(): bool
    {
        return in_array($this->status, ['approved', 'rewarded']);
    }
}

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
        'idea_no',
        'department_code',
        'idea_title',
        'idea_types',
        'file_path',
        'submission_date',
        'implemented_date',
        'idea_location',
        'score',
        'notes',
        'before_condition',
        'cause',
        'action',
        'result',
        'standardization',
        'benefit',
        'benefit_amount',
        'ldr_npk',
        'implementation_status',
        'ldr_status',
        'ldr_notes',
        'ldr_approved_at',
        'ldr_scores',
        'ldr_score_total',
        'spv_npk',
        'spv_notes',
        'spv_approved_at',
        'spv_status',
        'supervisor_decision',
        'supervisor_reason',
        'standard_review',
        'spv_scores',
        'spv_score_total',
        'kdp_npk',
        'kdp_notes',
        'kdp_approved_at',
        'kdp_status',
        'kdp_scores',
        'kdp_score_total',
        'admin_npk',
        'admin_notes',
        'admin_approved_at',
        'admin_status',
        'admin_scores',
        'admin_score_total',
        'final_score',
        'status',
        'final_approved_at',
        'reward_amount',
        'calculated_reward_amount',
        'paid_at',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'implemented_date' => 'date',
        'ldr_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'kdp_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'final_approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'score' => 'integer',
        'spv_scores' => 'array',
        'ldr_scores' => 'array',
        'kdp_scores' => 'array',
        'admin_scores' => 'array',
        'ldr_score_total' => 'integer',
        'spv_score_total' => 'integer',
        'kdp_score_total' => 'integer',
        'admin_score_total' => 'integer',
        'final_score' => 'integer',
        'idea_types' => 'array',
        'benefit_amount' => 'decimal:0',
        'reward_amount' => 'decimal:0',
        'calculated_reward_amount' => 'decimal:0',
    ];

    /**
     * Relasi ke pengaju ide (Employee).
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_npk', 'npk');
    }

    public function ldr()
    {
        return $this->belongsTo(Employee::class, 'ldr_npk', 'npk');
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

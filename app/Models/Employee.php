<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Occupation;
use App\Models\Role;
use App\Models\SubSection;
use App\Models\Section;
use App\Models\Department;

// Penting: Employee tidak perlu extends Authenticatable karena login sudah pakai User
class Employee extends Model
{
    use Notifiable;

    protected $table = 'm_employees';
    public $timestamps = false; // asumsikan tidak ada created_at/updated_at

    protected $fillable = [
        'nama',
        'npk',
        'phone',
        'line_code',
        'sub_section',
        'occupation',
        'transport',
        'status_emp',
        'employment_status',
        'gp',
        'quota_used_1',
        'quota_remain_1',
        'quota_used_2',
        'quota_remain_2',
        'quota_used_3',
        'quota_remain_3',
        'quota_used_4',
        'quota_remain_4',
        'quota_used_5',
        'quota_remain_5',
        'quota_used_6',
        'quota_remain_6',
        'quota_used_7',
        'quota_remain_7',
        'quota_used_8',
        'quota_remain_8',
        'quota_used_9',
        'quota_remain_9',
        'quota_used_10',
        'quota_remain_10',
        'quota_used_11',
        'quota_remain_11',
        'quota_used_12',
        'quota_remain_12',
    ];

    // Relasi ke User (kebalikan dari User::employee)
    public function user()
    {
        return $this->belongsTo(User::class, 'npk', 'npk');
    }

    public function job()
    {
        return $this->belongsTo(Occupation::class, 'occupation', 'code');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'sub_section', 'code');
    }

    public function subSection()
    {
        return $this->belongsTo(SubSection::class, 'sub_section', 'code');
    }

    public function getDeptCode()
    {
        if ($this->occupation === 'KDP') {
            $managedDept = Department::where('npk', $this->npk)->first();
            if ($managedDept) {
                return $managedDept->code;
            }
        }

        if ($this->subSection && $this->subSection->section) {
            return $this->subSection->section->code_department;
        }

        if ($this->section) {
            return $this->section->code_department;
        }

        return $this->line_code;
    }

    public function getDepartment()
    {
        $deptCode = $this->getDeptCode();
        if ($deptCode) {
            return Department::where('code', $deptCode)->first();
        }
        return null;
    }

    public function isSpv() { return $this->occupation === 'SPV'; }
    public function isKadept() { return $this->occupation === 'KDP'; }
    public function isLdr() { return $this->occupation === 'LDR'; }

    /** Hanya LDR yang boleh mengajukan SS atas nama operator (OPR) */
    public function canSubmitSsForOpr(): bool
    {
        return $this->occupation === 'LDR';
    }

    /** LDR, SPV, KDP: boleh mengajukan SS untuk diri sendiri */
    public function canSubmitOwnSs(): bool
    {
        return in_array($this->occupation, ['LDR', 'SPV', 'KDP'], true);
    }

    public function scopeOccupation($query, string $code)
    {
        return $query->where('occupation', $code);
    }

    public function scopeInDepartment($query, $deptCode)
    {
        return $query->where(function($q) use ($deptCode) {
            $q->whereHas('subSection.section', function($sq) use ($deptCode) {
                $sq->where('code_department', $deptCode);
            })
            ->orWhereHas('section', function($sq) use ($deptCode) {
                $sq->where('code_department', $deptCode);
            })
            ->orWhere('line_code', $deptCode)
            ->orWhereIn('npk', function($sq) use ($deptCode) {
                $sq->select('npk')
                    ->from('m_departments')
                    ->where('code', $deptCode);
            });
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\QccCircle;
use App\Models\SsSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function showWelcome()
    {
        $authUser = Auth::user();

        if (!$authUser) {
            Auth::logout();
            return redirect('/login');
        }

        $employee = $authUser->employee;

        if (!$employee) {
            Auth::logout();
            return redirect('/login')->with('error', 'Data karyawan tidak ditemukan. Hubungi administrator.');
        }

        $activeRole = session('active_role', 'employee');
        $roleLabel = match ($activeRole) {
            'admin' => 'Administrator',
            'employee' => 'Employee',
            default => ucfirst($activeRole),
        };

        [$qccQuery, $ssQuery, $scopeLabel, $scopeDescription, $scopeLevel] = $this->buildScopedQueries($employee, $activeRole);

        $jumlahQcc = (clone $qccQuery)->count();
        $jumlahSs = (clone $ssQuery)->count();

        $jumlahDeptTercakup = $this->countDepartmentsInScope($employee, $activeRole);

        $jumlahQccMembers = (clone $qccQuery)
            ->withCount('members')
            ->get()
            ->sum('members_count');

        $qccAvgMembers = $jumlahQcc > 0 ? round($jumlahQccMembers / $jumlahQcc, 1) : 0;

        $jumlahQccThisMonth = (clone $qccQuery)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $jumlahSsPending = (clone $ssQuery)
            ->whereIn('status', ['submitted', 'spv_review', 'kdp_review', 'admin_review'])
            ->count();

        $jumlahSsApproved = (clone $ssQuery)
            ->whereIn('status', ['approved', 'rewarded'])
            ->count();

        $jumlahSsRejected = (clone $ssQuery)
            ->where('status', 'rejected')
            ->count();

        $jumlahSsThisMonth = (clone $ssQuery)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $completionRate = $jumlahSs > 0 ? (int) round(($jumlahSsApproved / $jumlahSs) * 100) : 0;
        $pendingRate = $jumlahSs > 0 ? (int) round(($jumlahSsPending / $jumlahSs) * 100) : 0;
        $rejectedRate = $jumlahSs > 0 ? (int) round(($jumlahSsRejected / $jumlahSs) * 100) : 0;

        $latestQccAt = (clone $qccQuery)->latest('created_at')->value('created_at');
        $latestSsAt = (clone $ssQuery)->latest('created_at')->value('created_at');

        $latestQccAt = $latestQccAt ? Carbon::parse($latestQccAt) : null;
        $latestSsAt = $latestSsAt ? Carbon::parse($latestSsAt) : null;

        $lastUpdateAt = collect([$latestQccAt, $latestSsAt])
            ->filter()
            ->sortByDesc(fn ($date) => $date->timestamp)
            ->first();

        $user = $employee;

        return view('home', compact(
            'user',
            'roleLabel',
            'activeRole',
            'scopeLabel',
            'scopeDescription',
            'scopeLevel',
            'jumlahQcc',
            'jumlahQccMembers',
            'qccAvgMembers',
            'jumlahQccThisMonth',
            'jumlahSs',
            'jumlahSsPending',
            'jumlahSsApproved',
            'jumlahSsRejected',
            'jumlahSsThisMonth',
            'jumlahDeptTercakup',
            'completionRate',
            'pendingRate',
            'rejectedRate',
            'latestQccAt',
            'latestSsAt',
            'lastUpdateAt'
        ));
    }

    private function buildScopedQueries($employee, string $activeRole): array
    {
        $qccQuery = QccCircle::query();
        $ssQuery = SsSubmission::query();

        $scopeLabel = 'All Company';
        $scopeDescription = 'Seluruh data perusahaan';
        $scopeLevel = 'Company';

        if ($activeRole !== 'admin') {
            $deptCode = $employee->getDeptCode();

            if ($employee->occupation === 'GMR') {
                $myDept = Department::where('code', $deptCode)->first();
                $divCode = $myDept ? $myDept->code_division : null;
                $divName = $myDept && $myDept->division ? $myDept->division->name : 'N/A';

                if ($divCode) {
                    $qccQuery->whereHas('department', function ($q) use ($divCode) {
                        $q->where('code_division', $divCode);
                    });

                    $deptCodesInDiv = Department::where('code_division', $divCode)->pluck('code');
                    $ssQuery->whereIn('department_code', $deptCodesInDiv);
                } else {
                    $qccQuery->where('department_code', $deptCode);
                    $ssQuery->where('department_code', $deptCode);
                }

                $scopeLabel = 'Division: ' . $divName;
                $scopeDescription = 'Seluruh departemen dalam divisi yang sama';
                $scopeLevel = 'Division';
            } elseif (in_array($employee->occupation, ['KDP', 'SPV'])) {
                $qccQuery->where('department_code', $deptCode);
                $ssQuery->where('department_code', $deptCode);

                $deptName = optional($employee->getDepartment())->name ?? $deptCode;

                $scopeLabel = 'Department: ' . $deptName;
                $scopeDescription = 'Hanya data dari departemen sendiri';
                $scopeLevel = 'Department';
            } else {
                $qccQuery->whereHas('members', function ($q) use ($employee) {
                    $q->where('employee_npk', $employee->npk);
                });

                $ssQuery->where('employee_npk', $employee->npk);

                $scopeLabel = 'Personal & My Circle';
                $scopeDescription = 'Data personal dan circle yang diikuti';
                $scopeLevel = 'Personal';
            }
        }

        return [$qccQuery, $ssQuery, $scopeLabel, $scopeDescription, $scopeLevel];
    }

    private function countDepartmentsInScope($employee, string $activeRole): int
    {
        if ($activeRole === 'admin') {
            return Department::count();
        }

        $deptCode = $employee->getDeptCode();

        if ($employee->occupation === 'GMR') {
            $myDept = Department::where('code', $deptCode)->first();
            $divCode = $myDept ? $myDept->code_division : null;

            if ($divCode) {
                return Department::where('code_division', $divCode)->count();
            }

            return 1;
        }

        return 1;
    }
}
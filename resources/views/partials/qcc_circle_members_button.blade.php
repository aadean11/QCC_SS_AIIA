@php
    $circleMemberPayload = [
        'circle_name' => $circle->circle_name,
        'circle_code' => $circle->circle_code,
        'department_code' => $circle->department_code,
        'department' => $circle->department ? ['name' => $circle->department->name] : null,
        'active_theme' => $circle->activeTheme ? ['theme_name' => $circle->activeTheme->theme_name] : null,
        'members' => ($circle->members ?? collect())->map(fn ($m) => [
            'employee_npk' => $m->employee_npk,
            'role' => $m->role,
            'employee' => $m->employee ? ['nama' => $m->employee->nama] : null,
        ])->values()->all(),
    ];
@endphp
<button type="button"
    onclick="openCircleMembersModal({{ json_encode($circleMemberPayload) }})"
    class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-gray-50 text-gray-500 rounded-lg hover:bg-gray-200 transition-all shadow-sm"
    title="Detail Anggota">
    <i class="fa-solid fa-users text-[8px] md:text-[10px]"></i>
</button>

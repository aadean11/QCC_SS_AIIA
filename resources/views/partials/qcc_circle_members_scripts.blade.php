<script>
    function openCircleMembersModal(circle) {
        const members = circle.members || [];
        const activeTheme = circle.active_theme || circle.activeTheme || null;
        const department = circle.department || null;
        const deptName = department?.name || circle.department_code || '-';
        const themeName = activeTheme?.theme_name || 'Belum ada tema aktif';

        document.getElementById('circleMembersName').innerText = circle.circle_name || '-';
        document.getElementById('circleMembersMeta').innerText = `${circle.circle_code || '-'} | Dept: ${deptName}`;
        document.getElementById('circleMembersTheme').innerText = `Tema: ${themeName}`;
        document.getElementById('circleMembersCount').innerText = members.length;

        const container = document.getElementById('circleMembersList');
        container.innerHTML = '';

        if (members.length === 0) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-400 italic text-xs md:text-sm py-6">Belum ada anggota terdaftar.</p>';
        } else {
            members.forEach((member) => {
                const isLeader = member.role === 'LEADER';
                const badgeClass = isLeader
                    ? 'bg-amber-100 text-amber-600 border-amber-200'
                    : 'bg-blue-100 text-blue-600 border-blue-200';
                const initial = (member.employee?.nama || '?').charAt(0);

                container.innerHTML += `
                    <div class="flex items-center gap-3 md:gap-4 p-3 md:p-4 bg-gray-50 rounded-xl md:rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-lg md:rounded-xl flex items-center justify-center text-[#091E6E] font-black shadow-sm text-sm md:text-lg border border-blue-50">
                            ${initial}
                        </div>
                        <div class="flex-1 leading-tight min-w-0">
                            <p class="text-xs md:text-sm font-bold text-[#091E6E] truncate">${member.employee?.nama || 'Unknown'}</p>
                            <p class="text-[8px] md:text-[10px] text-gray-400 font-medium mb-1">${member.employee_npk || '-'}</p>
                            <span class="text-[7px] md:text-[9px] font-bold px-1.5 md:px-2 py-0.5 rounded-full border ${badgeClass}">${member.role || 'MEMBER'}</span>
                        </div>
                    </div>`;
            });
        }

        document.getElementById('modalCircleMembers').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
</script>

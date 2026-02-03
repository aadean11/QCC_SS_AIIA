@extends('welcome')

@section('title', 'Approve Circle Baru')

@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight">Approval Circle</li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Persetujuan Pendaftaran Circle Baru</h2>
            <p class="text-sm text-gray-400 italic font-medium">
                Departemen: 
                <span class="text-[#1035D1] uppercase font-bold">
                    @php $myDept = $user->getDepartment(); @endphp
                    @if($myDept)
                        {{ $myDept->name }}
                    @else
                        {{ $user->getDeptCode() ?: 'TIDAK TERDETEKSI' }}
                    @endif
                </span>
            </p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-end items-center">
            <!-- Form Filter -->
            <form action="{{ route('qcc.approval.circle') }}" method="GET" id="filterForm" class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E]">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Circle atau Kode..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm transition-all text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[2rem] p-6 shadow-sm border border-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold rounded-tl-2xl">Nama Circle & Kode</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold">Preview Anggota</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold text-center">Status</th>
                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-widest font-bold rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingCircles as $pc)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100 group">
                        <td class="px-6 py-4 rounded-l-xl border-y border-l border-gray-100 leading-tight">
                            <p class="font-bold text-[#091E6E] text-sm uppercase tracking-tight">{{ $pc->circle_name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter mt-1">{{ $pc->circle_code }} | DEPT: {{ $pc->department_code }}</p>
                        </td>
                        <td class="px-6 py-4 border-y border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="flex -space-x-2 overflow-hidden">
                                    @foreach($pc->members->take(3) as $m)
                                        <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-blue-100 flex items-center justify-center text-[10px] font-black text-[#091E6E] border border-blue-200" title="{{ $m->employee->nama ?? 'N/A' }}">
                                            {{ substr($m->employee->nama ?? '?', 0, 1) }}
                                        </div>
                                    @endforeach
                                </div>
                                @if($pc->members->count() > 3)
                                    <span class="text-[9px] font-bold text-gray-400">+{{ $pc->members->count() - 3 }} personil</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 border-y border-gray-100 text-center">
                            @php
                                $statusColor = 'text-amber-500 bg-amber-50 border-amber-100';
                                if($pc->status === 'ACTIVE') $statusColor = 'text-emerald-500 bg-emerald-50 border-emerald-100';
                                if($pc->status === 'WAITING KDP') $statusColor = 'text-blue-500 bg-blue-50 border-blue-100';
                                if($pc->status === 'WAITING SPV') $statusColor = 'text-yellow-500 bg-yellow-50 border-yellow-100';
                                if(str_contains($pc->status, 'REJECTED')) $statusColor = 'text-red-500 bg-red-50 border-red-100';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border {{ $statusColor }}">
                                 {{ $pc->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 rounded-r-xl border-y border-r border-gray-100 text-center">
                            @php
                                $canProcess = false;
                                if($user->occupation === 'SPV' && $pc->status === 'WAITING SPV') $canProcess = true;
                                if($user->occupation === 'KDP' && $pc->status === 'WAITING KDP') $canProcess = true;
                            @endphp

                            <div class="flex justify-center gap-2">
                                <!-- TOMBOL LIHAT DETAIL (MATA BIRU) -->
                                <button onclick="openViewDetail({{ json_encode($pc->load('members.employee')) }})" 
                                    class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Lihat Detail Kelompok">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>

                                @if($canProcess)
                                    <button onclick="openCircleAction('approve', {{ $pc->id }}, '{{ $pc->circle_name }}')" 
                                        class="bg-emerald-500 text-white px-5 py-2 rounded-xl text-[10px] font-bold uppercase shadow-lg shadow-emerald-100 hover:bg-emerald-600 active:scale-95 transition-all">
                                        Setujui
                                    </button>
                                    <button onclick="openCircleAction('reject', {{ $pc->id }}, '{{ $pc->circle_name }}')" 
                                        class="bg-red-500 text-white px-5 py-2 rounded-xl text-[10px] font-bold uppercase shadow-lg shadow-red-100 hover:bg-red-600 active:scale-95 transition-all">
                                        Tolak
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-20 text-gray-300 italic text-sm font-medium">
                            <i class="fa-solid fa-users-slash text-5xl block mb-3 opacity-20"></i>
                            Tidak ada pendaftaran Circle yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION AREA -->
        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-6">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $pendingCircles->firstItem() ?? 0 }} to {{ $pendingCircles->lastItem() ?? 0 }} of {{ $pendingCircles->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $pendingCircles->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL VIEW DETAIL MEMBER ================= -->
<div id="modalViewDetail" class="fixed inset-0 z-[110] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-2xl shadow-2xl animate-reveal overflow-hidden border border-white">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold uppercase tracking-widest">Detail Informasi Kelompok</h3>
                <button onclick="closeModal('modalViewDetail')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8 space-y-6">
                <!-- Circle Info Header -->
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-6 rounded-2xl border border-gray-100 shadow-inner font-medium">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Circle</label>
                        <p id="detCircleName" class="text-[#091E6E] font-black text-xl"></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kode & Dept</label>
                        <p id="detCircleCode" class="text-[#091E6E] font-bold"></p>
                    </div>
                </div>

                <!-- Member List Section -->
                <div>
                    <h4 class="text-xs font-bold text-[#091E6E] uppercase tracking-widest mb-4 border-b pb-2">Daftar Anggota Tim</h4>
                    <div id="memberDetailList" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Content via JS -->
                    </div>
                </div>

                <div class="pt-4">
                    <button onclick="closeModal('modalViewDetail')" class="w-full py-4 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-gray-200 transition-all">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL KONFIRMASI APPROVAL/REJECT (STAY SAME) ================= -->
<div id="modalCircleAction" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden border border-white">
            <div id="circleHeader" class="p-6 text-white flex justify-between items-center transition-colors duration-500">
                <h3 id="circleTitle" class="text-lg font-bold uppercase tracking-widest">Konfirmasi</h3>
                <button onclick="closeModal('modalCircleAction')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            
            <form id="formCircleAction" action="" method="POST" class="p-8 space-y-6 text-left">
                @csrf
                <input type="hidden" name="action" id="circleActionInput">
                <div class="mb-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block ml-1 leading-none">Pendaftaran Grup:</label>
                    <h4 id="circleNameDisplay" class="text-xl font-black text-[#091E6E] uppercase italic mt-1 leading-tight"></h4>
                </div>
                <div id="circleNoteArea" class="hidden animate-reveal">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 block mb-1">Alasan Penolakan Pendaftaran</label>
                    <textarea name="note" id="circleRejectionNote" rows="3" placeholder="Sebutkan alasan penolakan..." class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none font-medium"></textarea>
                </div>
                <p id="circleDescription" class="text-sm text-gray-600 font-medium leading-relaxed"></p>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('modalCircleAction')" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold uppercase text-[10px] tracking-widest">Batal</button>
                    <button type="submit" id="btnCircleSubmit" class="flex-1 py-4 text-white rounded-2xl font-bold shadow-lg uppercase text-[10px] tracking-widest transition-all active:scale-95"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Pagination Styling Horizontal */
    .custom-pagination nav { display: flex; align-items: center; justify-content: center; gap: 4px; }
    .custom-pagination nav svg { width: 1rem; height: 1rem; }
    .custom-pagination span[aria-current="page"] > span { 
        background-color: #091E6E !important; color: white !important; border: none !important;
        border-radius: 8px !important; padding: 6px 12px !important; font-size: 11px !important; font-weight: 800;
        box-shadow: 0 4px 6px -1px rgba(9, 30, 110, 0.2);
    }
    .custom-pagination a, .custom-pagination span { 
        border-radius: 8px !important; padding: 6px 12px !important; font-size: 11px !important;
        font-weight: 700 !important; border: 1px solid #edf2f7 !important; color: #64748b;
        transition: all 0.2s ease;
    }
    .custom-pagination a:hover { background-color: #f8fafc !important; border-color: #091E6E !important; color: #091E6E !important; }
</style>

<script>
    // --- FUNGSI LIAT DETAIL ---
    function openViewDetail(circle) {
        document.getElementById('detCircleName').innerText = circle.circle_name;
        document.getElementById('detCircleCode').innerText = circle.circle_code + " | Dept: " + circle.department_code;
        
        const container = document.getElementById('memberDetailList');
        container.innerHTML = '';

        circle.members.forEach(m => {
            const isLeader = m.role === 'LEADER';
            const badgeClass = isLeader ? 'bg-amber-100 text-amber-600 border-amber-200' : 'bg-blue-100 text-blue-600 border-blue-200';
            
            container.innerHTML += `
                <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="w-10 h-10 bg-blue-50 text-[#091E6E] rounded-lg flex items-center justify-center text-sm font-black border border-blue-100">
                        ${(m.employee?.nama || '?').charAt(0)}
                    </div>
                    <div class="flex-1 leading-none">
                        <p class="text-xs font-bold text-[#091E6E] mb-1">${m.employee?.nama || 'Unknown'}</p>
                        <p class="text-[9px] text-gray-400 font-bold mb-2 uppercase tracking-tighter">${m.employee_npk}</p>
                        <span class="text-[8px] font-bold px-2 py-0.5 rounded-full border ${badgeClass}">${m.role}</span>
                    </div>
                </div>
            `;
        });

        openModal('modalViewDetail');
    }

    // --- FUNGSI AKSI (APPROVE/REJECT) ---
    function openCircleAction(action, id, name) {
        const form = document.getElementById('formCircleAction');
        const title = document.getElementById('circleTitle');
        const header = document.getElementById('circleHeader');
        const btn = document.getElementById('btnCircleSubmit');
        const noteArea = document.getElementById('circleNoteArea');
        const desc = document.getElementById('circleDescription');
        const actionInput = document.getElementById('circleActionInput');
        const nameDisplay = document.getElementById('circleNameDisplay');

        form.action = `/qcc/approval/circle/process/${id}`;
        actionInput.value = action;
        nameDisplay.innerText = name;

        if (action === 'approve') {
            title.innerText = "Setujui Kelompok";
            header.className = "p-6 text-white flex justify-between items-center bg-emerald-500 shadow-lg";
            btn.className = "flex-1 py-4 bg-emerald-500 text-white rounded-2xl font-bold shadow-lg uppercase text-[10px] hover:bg-emerald-600 active:scale-95 transition-all";
            btn.innerText = "Ya, Setujui";
            desc.innerText = "Apakah Anda menyetujui pembentukan kelompok QCC ini beserta susunan anggotanya?";
            noteArea.classList.add('hidden');
            document.getElementById('circleRejectionNote').required = false;
        } else {
            title.innerText = "Tolak Kelompok";
            header.className = "p-6 text-white flex justify-between items-center bg-red-500 shadow-lg";
            btn.className = "flex-1 py-4 bg-red-500 text-white rounded-2xl font-bold shadow-lg uppercase text-[10px] hover:bg-red-600 active:scale-95 transition-all";
            btn.innerText = "Kirim Penolakan";
            desc.innerText = "";
            noteArea.classList.remove('hidden');
            document.getElementById('circleRejectionNote').required = true;
        }
        openModal('modalCircleAction');
    }

    function openModal(id) { 
        document.getElementById(id).classList.remove('hidden'); 
        document.body.style.overflow = 'hidden'; 
    }
    function closeModal(id) { 
        document.getElementById(id).classList.add('hidden'); 
        document.body.style.overflow = 'auto'; 
    }
</script>
@endpush
@extends('welcome')

@section('title', 'Approve Circle Baru')

@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">Menu Approval</li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight">Persetujuan Circle Baru</li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Persetujuan Circle Baru</h2>
            <p class="text-sm text-gray-400 italic">Daftar pendaftaran kelompok yang membutuhkan otorisasi Anda</p>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-blue-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-50 text-[#130998] rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase">Antrean</p>
                <p class="text-lg font-black text-[#091E6E]">{{ $pendingCircles->count() }} <span class="text-[10px] font-normal">Grup</span></p>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[2rem] p-6 shadow-sm border border-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold rounded-tl-2xl">Nama Circle & Kode</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold">Anggota Kelompok</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold text-center">Status Saat Ini</th>
                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-widest font-bold rounded-tr-2xl text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingCircles as $pc)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100">
                        <td class="px-6 py-4 rounded-l-xl border-y border-l">
                            <p class="font-bold text-[#091E6E] text-sm uppercase leading-tight">{{ $pc->circle_name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter mt-1">{{ $pc->circle_code }} | DEPT: {{ $pc->department_code }}</p>
                        </td>
                        <td class="px-6 py-4 border-y">
                            <div class="flex items-center gap-2">
                                <div class="flex -space-x-2 overflow-hidden">
                                    @foreach($pc->members->take(3) as $m)
                                        <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-blue-100 flex items-center justify-center text-[10px] font-black text-[#091E6E]" title="{{ $m->employee->nama }}">
                                            {{ substr($m->employee->nama, 0, 1) }}
                                        </div>
                                    @endforeach
                                </div>
                                @if($pc->members->count() > 3)
                                    <span class="text-[10px] font-bold text-gray-400">+{{ $pc->members->count() - 3 }} lainnya</span>
                                @else
                                    <span class="text-[10px] font-bold text-gray-400">{{ $pc->members->count() }} orang</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 border-y text-center text-xs font-bold text-amber-500 italic">
                             {{ $pc->status }}
                        </td>
                        <td class="px-6 py-4 rounded-r-xl border-y border-r text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick="openCircleAction('approve', {{ $pc->id }}, '{{ $pc->circle_name }}')" 
                                    class="bg-emerald-500 text-white px-4 py-2 rounded-xl text-[10px] font-bold uppercase shadow-lg shadow-emerald-100 hover:bg-emerald-600 active:scale-95 transition-all">
                                    Setujui
                                </button>
                                <button onclick="openCircleAction('reject', {{ $pc->id }}, '{{ $pc->circle_name }}')" 
                                    class="bg-red-500 text-white px-4 py-2 rounded-xl text-[10px] font-bold uppercase shadow-lg shadow-red-100 hover:bg-red-600 active:scale-95 transition-all">
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-20">
                            <div class="flex flex-col items-center gap-3 text-gray-300">
                                <i class="fa-solid fa-users-slash text-6xl"></i>
                                <p class="italic text-sm font-medium">Tidak ada pendaftaran Circle yang menunggu persetujuan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL KONFIRMASI CIRCLE ================= -->
<div id="modalCircleAction" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden border border-white">
            <div id="circleHeader" class="p-6 text-white flex justify-between items-center transition-colors duration-500">
                <h3 id="circleTitle" class="text-lg font-bold uppercase tracking-widest">Konfirmasi</h3>
                <button onclick="closeModal('modalCircleAction')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            
            <form id="formCircleAction" action="" method="POST" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="action" id="circleActionInput">
                
                <div class="mb-2">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pendaftaran Grup:</p>
                    <h4 id="circleNameDisplay" class="text-xl font-black text-[#091E6E] uppercase italic"></h4>
                </div>

                <!-- Input Note (Hanya muncul jika REJECT) -->
                <div id="circleNoteArea" class="hidden">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Alasan Penolakan Pendaftaran</label>
                    <textarea name="note" id="circleRejectionNote" rows="3" placeholder="Sebutkan alasan mengapa kelompok ini ditolak..." 
                        class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none font-medium"></textarea>
                </div>

                <p id="circleDescription" class="text-sm text-gray-600 font-medium leading-relaxed"></p>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('modalCircleAction')" class="flex-1 py-4 bg-gray-100 text-gray-400 rounded-2xl font-bold uppercase text-[10px] tracking-widest hover:bg-gray-200 transition-all">Batal</button>
                    <button type="submit" id="btnCircleSubmit" class="flex-1 py-4 text-white rounded-2xl font-bold shadow-lg uppercase text-[10px] tracking-widest transition-all"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
</script>
@endpush
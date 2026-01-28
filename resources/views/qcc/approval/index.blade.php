@extends('welcome')

@section('title', 'Waiting Approval QCC')

@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight">Waiting Approval</li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Persetujuan Progres QCC</h2>
            <p class="text-sm text-gray-400 italic">
                Mode Akses: <span class="font-bold text-[#1035D1] uppercase">{{ $user->occupation == 'KDP' ? 'Kepala Departemen' : 'Supervisor' }}</span>
            </p>
        </div>
        
        <!-- Quick Stats Mini -->
        <div class="flex gap-4">
            <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-blue-100 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 text-[#091E6E] rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase">Menunggu</p>
                    <p class="text-lg font-black text-[#091E6E]">{{ $pendingSteps->count() }} <span class="text-[10px] font-normal text-gray-400">Berkas</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[2rem] p-6 shadow-sm border border-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold rounded-tl-2xl">Info Circle & Leader</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold">Langkah (PDCA)</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold text-center">Berkas</th>
                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-widest font-bold rounded-tr-2xl">Aksi Persetujuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingSteps as $ps)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100">
                        <td class="px-6 py-4 rounded-l-xl border-y border-l">
                            <p class="font-bold text-[#091E6E] text-sm uppercase tracking-tight">{{ $ps->circle->circle_name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Leader: {{ $ps->uploader->nama }} ({{ $ps->upload_by }})</p>
                        </td>
                        <td class="px-6 py-4 border-y">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-50 text-[#091E6E] rounded-lg flex items-center justify-center font-black text-xs border border-blue-100">
                                    {{ $ps->step->step_number }}
                                </div>
                                <span class="text-xs font-bold text-gray-600 uppercase">{{ $ps->step->step_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-y text-center">
                            <a href="{{ asset($ps->file_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-gray-50 text-[#091E6E] px-4 py-2 rounded-xl text-[10px] font-bold hover:bg-[#091E6E] hover:text-white transition-all border border-gray-100 shadow-sm">
                                <i class="fa-solid fa-file-pdf"></i> PREVIEW
                            </a>
                        </td>
                        <td class="px-6 py-4 rounded-r-xl border-y border-r text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick="openApprovalModal('approve', {{ $ps->id }}, '{{ $ps->circle->circle_name }}')" 
                                    class="bg-emerald-500 text-white px-5 py-2 rounded-xl text-[10px] font-bold uppercase shadow-lg shadow-emerald-100 hover:bg-emerald-600 active:scale-95 transition-all">
                                    Setujui
                                </button>
                                <button onclick="openApprovalModal('reject', {{ $ps->id }}, '{{ $ps->circle->circle_name }}')" 
                                    class="bg-red-500 text-white px-5 py-2 rounded-xl text-[10px] font-bold uppercase shadow-lg shadow-red-100 hover:bg-red-600 active:scale-95 transition-all">
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-20">
                            <div class="flex flex-col items-center gap-3 text-gray-300">
                                <i class="fa-solid fa-clipboard-check text-6xl"></i>
                                <p class="italic text-sm font-medium">Semua tugas sudah selesai. Tidak ada antrean permohonan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL APPROVAL / REJECT ================= -->
<div id="modalApproval" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden border border-white">
            <!-- Header Modal Dinamis -->
            <div id="modalHeader" class="p-6 text-white flex justify-between items-center transition-colors duration-500">
                <h3 id="modalTitle" class="text-lg font-bold uppercase tracking-widest">Konfirmasi</h3>
                <button onclick="closeModal('modalApproval')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            
            <form id="formApproval" action="" method="POST" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="action" id="inputAction">
                
                <div class="mb-2">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Circle Terkait:</p>
                    <h4 id="displayCircleName" class="text-xl font-black text-[#091E6E]"></h4>
                </div>

                <!-- Input Note (Hanya muncul jika REJECT) -->
                <div id="noteArea" class="hidden animate-reveal">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Alasan Penolakan (Wajib)</label>
                    <textarea name="note" id="rejectionNote" rows="3" placeholder="Sebutkan alasan atau poin yang perlu diperbaiki..." 
                        class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none font-medium transition-all"></textarea>
                </div>

                <p id="modalDescription" class="text-sm text-gray-600 font-medium leading-relaxed"></p>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('modalApproval')" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold uppercase text-[10px] tracking-widest hover:bg-gray-200 transition-all">Batal</button>
                    <button type="submit" id="btnSubmitApproval" class="flex-1 py-4 text-white rounded-2xl font-bold shadow-lg uppercase text-[10px] tracking-widest transition-all"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openApprovalModal(action, id, circleName) {
        const form = document.getElementById('formApproval');
        const title = document.getElementById('modalTitle');
        const header = document.getElementById('modalHeader');
        const btn = document.getElementById('btnSubmitApproval');
        const noteArea = document.getElementById('noteArea');
        const desc = document.getElementById('modalDescription');
        const actionInput = document.getElementById('inputAction');
        const displayCircle = document.getElementById('displayCircleName');

        // Reset & Setup
        form.action = `/qcc/approval/process/${id}`;
        actionInput.value = action;
        displayCircle.innerText = circleName;

        if (action === 'approve') {
            title.innerText = "Setujui Progres";
            header.className = "p-6 text-white flex justify-between items-center bg-emerald-500";
            btn.className = "flex-1 py-4 bg-emerald-500 text-white rounded-2xl font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-600 active:scale-95 transition-all";
            btn.innerText = "Ya, Setujui";
            desc.innerText = "Apakah Anda sudah memverifikasi isi dokumen dan menyatakan bahwa progres ini LAYAK untuk dilanjutkan?";
            noteArea.classList.add('hidden');
            document.getElementById('rejectionNote').required = false;
        } else {
            title.innerText = "Tolak Progres";
            header.className = "p-6 text-white flex justify-between items-center bg-red-500";
            btn.className = "flex-1 py-4 bg-red-500 text-white rounded-2xl font-bold shadow-lg shadow-red-200 hover:bg-red-600 active:scale-95 transition-all";
            btn.innerText = "Kirim Penolakan";
            desc.innerText = "";
            noteArea.classList.remove('hidden');
            document.getElementById('rejectionNote').required = true;
        }

        openModal('modalApproval');
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
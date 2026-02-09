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
            <p class="text-sm text-gray-400 italic font-medium">
                Departemen: 
                <span class="text-[#1035D1] uppercase font-bold">
                    @php $myDept = $user->getDepartment(); @endphp
                    @if($myDept)
                        {{ $myDept->name }}
                    @else
                        {{ $user->getDeptCode() ?: 'DEPARTEMEN TIDAK TERDETEKSI' }}
                    @endif
                </span>
            </p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-end items-center">
            <!-- Form Per Page & Search -->
            <form action="{{ route('qcc.approval.progress') }}" method="GET" id="filterForm" class="flex items-center gap-3">
                <!-- Dropdown Show Entries -->
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E]">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Circle atau Nama..." 
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
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold rounded-tl-2xl">Info Circle & Leader</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold">Langkah (PDCA)</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold text-center">Berkas</th>
                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-widest font-bold rounded-tr-2xl">Aksi Persetujuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingSteps as $ps)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100 group">
                        <td class="px-6 py-3 rounded-l-xl border-y border-l border-gray-100">
                            <p class="font-bold text-[#091E6E] text-sm uppercase tracking-tight">{{ $ps->circle->circle_name ?? 'N/A' }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Leader: {{ $ps->uploader->nama ?? 'N/A' }} ({{ $ps->upload_by ?? 'N/A' }})</p>
                        </td>
                        <td class="px-6 py-3 border-y border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-50 text-[#091E6E] rounded-lg flex items-center justify-center font-black text-xs border border-blue-100 group-hover:bg-[#091E6E] group-hover:text-white transition-all">
                                    {{ $ps->step->step_number ?? 'N/A' }}
                                </div>
                                <span class="text-xs font-bold text-gray-600 uppercase">{{ $ps->step->step_name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 border-y border-gray-100 text-center">
                            @if($ps->file_path)
                                @php
                                    $extension = pathinfo($ps->file_path, PATHINFO_EXTENSION);
                                    $fileUrl = asset('storage/' . $ps->file_path);
                                @endphp

                                @if(strtolower($extension) === 'pdf')
                                    <button onclick="openFilePreview('{{ $fileUrl }}', '{{ $ps->file_name ?? 'Document' }}')" 
                                        class="inline-flex items-center gap-2 bg-blue-50 text-[#091E6E] px-4 py-2 rounded-xl text-[10px] font-bold hover:bg-[#091E6E] hover:text-white transition-all border border-blue-100 shadow-sm active:scale-95">
                                        <i class="fa-solid fa-eye"></i> PREVIEW
                                    </button>
                                @else
                                    <a href="{{ $fileUrl }}" download class="inline-flex items-center gap-2 bg-amber-50 text-amber-700 px-4 py-2 rounded-xl text-[10px] font-bold hover:bg-amber-600 hover:text-white transition-all border border-amber-100 shadow-sm active:scale-95">
                                        <i class="fa-solid fa-download"></i> DOWNLOAD
                                    </a>
                                @endif
                            @else
                                <span class="text-xs text-gray-400 italic">No file</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            @php
                                $canProcess = false;
                                if($user->occupation === 'SPV' && $ps->status === 'WAITING SPV') $canProcess = true;
                                if($user->occupation === 'KDP' && $ps->status === 'WAITING KDP') $canProcess = true;
                            @endphp

                            @if($canProcess)
                                <div class="flex justify-center gap-2">
                                    <button onclick="openApprovalModal('approve', {{ $ps->id }}, '{{ $ps->circle->circle_name ?? "Circle" }}')" 
                                        class="bg-emerald-500 text-white px-5 py-2 rounded-xl text-[10px] font-bold uppercase shadow-lg shadow-emerald-100 hover:bg-emerald-600 active:scale-95 transition-all">
                                        Setujui
                                    </button>
                                    <button onclick="openApprovalModal('reject', {{ $ps->id }}, '{{ $ps->circle->circle_name ?? "Circle" }}')" 
                                        class="bg-red-500 text-white px-5 py-2 rounded-xl text-[10px] font-bold uppercase shadow-lg shadow-red-100 hover:bg-red-600 active:scale-95 transition-all">
                                        Tolak
                                    </button>
                                </div>
                            @else
                                @php
                                    $badgeColor = 'bg-gray-100 text-gray-500 border-gray-200';
                                    if($ps->status === 'APPROVED') $badgeColor = 'bg-emerald-100 text-emerald-600 border-emerald-200';
                                    if($ps->status === 'WAITING KDP') $badgeColor = 'bg-blue-100 text-blue-600 border-blue-200';
                                    if($ps->status === 'WAITING SPV') $badgeColor = 'bg-yellow-100 text-yellow-600 border-yellow-200';
                                    if(str_contains($ps->status, 'REJECTED')) $badgeColor = 'bg-red-100 text-red-600 border-red-200';
                                @endphp
                                <span class="px-4 py-2 rounded-xl text-[10px] font-black uppercase border {{ $badgeColor }}">
                                    {{ $ps->status }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-20 text-gray-300 italic text-sm">
                            <i class="fa-solid fa-folder-open text-4xl block mb-2"></i>
                            Tidak ada data permohonan ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION AREA -->
        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-6">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $pendingSteps->firstItem() ?? 0 }} to {{ $pendingSteps->lastItem() ?? 0 }} of {{ $pendingSteps->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $pendingSteps->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL APPROVAL / REJECT ================= -->
<div id="modalApproval" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
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

<!-- ================= MODAL PREVIEW PDF ================= -->
<div id="modalFilePreview" class="fixed inset-0 z-[110] hidden overflow-y-auto bg-black/70 backdrop-blur-md">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-[2rem] w-full max-w-5xl h-[90vh] shadow-2xl animate-reveal overflow-hidden flex flex-col">
            <!-- Header Modal -->
            <div class="sidebar-gradient p-5 text-white flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-file-pdf text-xl"></i>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-widest">Preview Dokumen Progres</h3>
                        <p id="previewFileName" class="text-[10px] text-blue-200 truncate max-w-xs italic"></p>
                    </div>
                </div>
                <button onclick="closeModal('modalFilePreview')" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/20 transition-all text-2xl">&times;</button>
            </div>
            
            <!-- Area Iframe (Isi File) -->
            <div class="flex-1 bg-gray-100">
                <iframe id="fileIframe" src="" class="w-full h-full border-none"></iframe>
            </div>

            <!-- Footer Modal -->
            <div class="p-4 bg-white border-t flex justify-between items-center px-8">
                <p class="text-[10px] text-gray-400 italic">* Gunakan menu di dalam preview jika ingin mencetak (print).</p>
                <a id="btnDownloadInPreview" href="" download class="bg-[#091E6E] text-white px-6 py-2 rounded-xl text-[10px] font-bold uppercase shadow-lg hover:bg-[#130998] transition-all">
                    Download File
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Styling Paging Horizontal */
    .custom-pagination nav { display: flex; align-items: center; justify-content: center; gap: 4px; }
    .custom-pagination nav svg { width: 1rem; height: 1rem; }
    .custom-pagination span[aria-current="page"] > span { 
        background-color: #091E6E !important; color: white !important; border: none !important;
        border-radius: 8px !important; padding: 6px 12px !important; font-size: 11px !important; font-weight: 800;
    }
    .custom-pagination a, .custom-pagination span { 
        border-radius: 8px !important; padding: 6px 12px !important; font-size: 11px !important;
        font-weight: 700 !important; border: 1px solid #edf2f7 !important; color: #64748b;
        transition: all 0.2s ease;
    }
</style>
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
        form.action = `/qcc/approval/progress/process/${id}`;
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

    function openFilePreview(url, fileName) {
        const iframe = document.getElementById('fileIframe');
        const nameDisplay = document.getElementById('previewFileName');
        const downloadBtn = document.getElementById('btnDownloadInPreview');

        // Masukkan URL file ke Iframe
        iframe.src = url;
        nameDisplay.innerText = fileName;
        downloadBtn.href = url;

        // Tampilkan Modal Preview
        openModal('modalFilePreview');
    }

    // Tambahan: Reset Iframe saat modal ditutup agar tidak membebani memori
    function closeModal(id) {
        if (id === 'modalFilePreview') {
            document.getElementById('fileIframe').src = '';
        }
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
@endpush
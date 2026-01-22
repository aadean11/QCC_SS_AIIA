@extends('welcome')

@section('title', 'Master Steps QCC')

@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight">Master Steps</li>
        </ol>
    </nav>

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Master Steps QCC</h2>
            <p class="text-sm text-gray-400">Pengaturan 8 Langkah Standar QCC</p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-end items-center">
            <!-- Form Per Page & Search -->
            <form action="{{ route('qcc.admin.master_steps') }}" method="GET" id="filterForm" class="flex items-center gap-3">
                <!-- Dropdown Show Entries -->
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E]">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari langkah..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm transition-all text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </form>

            <button onclick="openModal('modalAdd')" class="bg-[#091E6E] hover:bg-[#130998] text-white px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg transition-all active:scale-95 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-plus"></i> Tambah
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[2rem] p-5 shadow-sm border border-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-spacing-y-2">
                <thead>
                    <tr class="sidebar-gradient">
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl">
                            No. Step
                        </th>

                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold">
                            Nama Langkah
                        </th>

                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($steps as $step)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all group shadow-sm border border-gray-100">
                        <td class="px-6 py-3 rounded-l-xl border-y border-l border-gray-100">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-[#091E6E] font-bold flex items-center justify-center border border-blue-100 text-xs group-hover:bg-[#091E6E] group-hover:text-white transition-all duration-300">
                                {{ $step->step_number }}
                            </div>
                        </td>
                        <td class="px-6 py-3 border-y border-gray-100">
                            <div class="flex flex-col">
                                <span class="font-bold text-[#091E6E] text-sm group-hover:text-[#130998]">{{ $step->step_name }}</span>
                                <span class="text-[11px] text-gray-400 italic line-clamp-1 max-w-lg">{{ $step->description ?? 'Tidak ada deskripsi tersedia.' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick="openDetailModal({{ $step }})" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Detail">
                                    <i class="fa-solid fa-eye text-[10px]"></i>
                                </button>
                                <button onclick="openEditModal({{ $step }})" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </button>
                                <button onclick="confirmDelete('{{ $step->id }}')" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Hapus">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                                <form id="delete-form-{{ $step->id }}" action="{{ route('qcc.admin.delete_step', $step->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-10">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <i class="fa-solid fa-folder-open text-4xl"></i>
                                <span class="italic text-sm">Data tidak ditemukan...</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION AREA -->
        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-6">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $steps->firstItem() ?? 0 }} to {{ $steps->lastItem() ?? 0 }} of {{ $steps->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $steps->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL -->
<div id="modalDetail" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold">Detail Langkah QCC</h3>
                <button onclick="closeModal('modalDetail')" class="text-white/70 hover:text-white transition-all text-2xl">&times;</button>
            </div>
            <div class="p-8 space-y-6">
                <div class="flex items-start gap-4">
                    <div id="det_number" class="text-4xl font-black text-gray-100 bg-[#091E6E] w-16 h-16 rounded-2xl flex items-center justify-center shadow-inner">0</div>
                    <div class="flex-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Nama Langkah</label>
                        <h2 id="det_name" class="text-2xl font-bold text-[#091E6E] leading-tight">-</h2>
                    </div>
                </div>
                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 block">Deskripsi Lengkap</label>
                    <p id="det_desc" class="text-gray-600 leading-relaxed italic text-sm">-</p>
                </div>
                <button onclick="closeModal('modalDetail')" class="w-full py-4 bg-gray-100 text-gray-500 rounded-xl font-bold hover:bg-gray-200 transition-all uppercase tracking-widest text-xs">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADD -->
<div id="modalAdd" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">  
            <!-- Header -->
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold">Tambah Step QCC</h3>
                <button onclick="closeModal('modalAdd')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <!-- Body -->
            <form id="formAdd" action="{{ route('qcc.admin.store_step') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Nomor Step</label>
                    <input type="number" name="step_number" required
                        class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl
                               focus:ring-2 focus:ring-[#091E6E] outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Nama Langkah</label>
                    <input type="text" name="step_name" required
                        class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl
                               focus:ring-2 focus:ring-[#091E6E] outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl
                               focus:ring-2 focus:ring-[#091E6E] outline-none transition-all"></textarea>
                </div>
                <button type="submit"
                    class="w-full py-4 bg-[#091E6E] text-white rounded-xl font-bold shadow-lg
                           hover:bg-[#130998] transition-all uppercase tracking-widest text-xs">
                    Simpan Data
                </button>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <!-- Header -->
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold">Edit Step QCC</h3>
                <button onclick="closeModal('modalEdit')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <!-- Body -->
            <form id="formEdit" method="POST" class="p-8 space-y-6">
                @csrf @method('PUT')
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Nomor Step</label>
                    <input type="number" name="step_number" id="edit_number" required
                        class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl
                               focus:ring-2 focus:ring-amber-500 outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Nama Langkah</label>
                    <input type="text" name="step_name" id="edit_name" required
                        class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl
                               focus:ring-2 focus:ring-amber-500 outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Deskripsi</label>
                    <textarea name="description" id="edit_desc" rows="3"
                        class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl
                               focus:ring-2 focus:ring-amber-500 outline-none transition-all"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('modalEdit')"
                        class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-xl font-bold
                               hover:bg-gray-200 transition-all uppercase tracking-widest text-xs">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-4 bg-amber-500 text-white rounded-xl font-bold shadow-lg
                               hover:bg-amber-600 transition-all uppercase tracking-widest text-xs">
                        Update Data
                    </button>
                </div>
            </form>
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
        background-color: #091E6E !important; 
        color: white !important; 
        border: none !important;
        border-radius: 8px !important; 
        padding: 6px 12px !important;
        font-size: 11px !important;
        font-weight: 800;
        box-shadow: 0 4px 6px -1px rgba(9, 30, 110, 0.2);
    }
    .custom-pagination a, .custom-pagination span { 
        border-radius: 8px !important; 
        padding: 6px 12px !important; 
        font-size: 11px !important;
        font-weight: 700 !important;
        border: 1px solid #edf2f7 !important;
        color: #64748b;
        transition: all 0.2s ease;
    }
    .custom-pagination a:hover { 
        background-color: #f8fafc !important; 
        border-color: #091E6E !important; 
        color: #091E6E !important; 
    }
</style>

<script>
    function openModal(id) { 
        document.getElementById(id).classList.remove('hidden'); 
        document.body.style.overflow = 'hidden'; 
    }
    function closeModal(id) { 
        document.getElementById(id).classList.add('hidden'); 
        document.body.style.overflow = 'auto'; 
    }

    function openDetailModal(step) {
        document.getElementById('det_number').innerText = step.step_number;
        document.getElementById('det_name').innerText = step.step_name;
        document.getElementById('det_desc').innerText = step.description || 'Tidak ada deskripsi.';
        openModal('modalDetail');
    }

    function openEditModal(step) {
        document.getElementById('formEdit').action = `/qcc/admin/master-steps/${step.id}`;
        document.getElementById('edit_number').value = step.step_number;
        document.getElementById('edit_name').value = step.step_name;
        document.getElementById('edit_desc').value = step.description;
        openModal('modalEdit');
    }

    @if(Session::has('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ Session::get('success') }}",
            showConfirmButton: false,
            timer: 2000,
            background: '#ffffff',
            iconColor: '#10B981',
            customClass: { title: 'text-[#091E6E] font-bold' }
        });
    @endif

    document.getElementById('formAdd').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Simpan data baru?',
            text: "Pastikan data yang diinput sudah benar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#091E6E',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) { this.submit(); } });
    });

    document.getElementById('formEdit').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Update data ini?',
            text: "Data lama akan digantikan dengan data baru.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#F59E0B',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) { this.submit(); } });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Step?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) { document.getElementById('delete-form-' + id).submit(); } })
    }
</script>
@endpush
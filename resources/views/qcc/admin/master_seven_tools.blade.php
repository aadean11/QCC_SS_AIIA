@extends('welcome')

@section('title', 'Master Seven Tools - SIGITA')

@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-4 md:mb-6 text-xs md:text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight text-[10px] md:text-xs">Master Seven Tools</li>
        </ol>
    </nav>

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Master Seven Tools</h2>
            <p class="text-xs md:text-sm text-gray-400">Kelola Daftar Alat Bantu & Template Dokumen Standar</p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-start md:justify-end items-center">
            <form action="{{ route('qcc.admin.master_seven_tools') }}" method="GET" id="filterForm" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E] w-full sm:w-auto">
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent w-full sm:w-auto">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tool..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm transition-all text-xs md:text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] md:text-xs"></i>
                </div>
            </form>

            <button onclick="openModal('modalAdd')" class="bg-[#091E6E] hover:bg-[#130998] text-white px-4 md:px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg transition-all active:scale-95 text-[10px] md:text-xs font-bold uppercase tracking-wider w-full sm:w-auto justify-center">
                <i class="fa-solid fa-plus"></i> Tambah
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-5 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[900px] md:min-w-full">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-2 md:px-4 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Nama Tool</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Deskripsi</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Template File</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tools as $tool)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all group shadow-sm border border-gray-100">
                        <td class="px-2 md:px-4 py-2 md:py-3 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500 text-xs md:text-sm">
                            {{ ($tools->currentPage() - 1) * $tools->perPage() + $loop->iteration }}
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            <span class="font-bold text-[#091E6E] text-xs md:text-sm group-hover:text-[#130998]">{{ $tool->tool_name }}</span>
                        </td>
                        
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            <span class="text-[8px] md:text-[10px] text-gray-400 italic line-clamp-1 max-w-xs md:max-w-sm">
                                {{ $tool->description ?? 'Tidak ada deskripsi tersedia.' }}
                            </span>
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            @if($tool->template_file_path)
                                <span class="text-[7px] md:text-[9px] bg-emerald-50 text-emerald-600 px-1.5 md:px-2 py-0.5 md:py-1 rounded-md font-bold flex items-center gap-1 w-fit border border-emerald-100 uppercase tracking-tighter">
                                    <i class="fa-solid fa-file-circle-check"></i> TERSEDIA
                                </span>
                            @else
                                <span class="text-[7px] md:text-[9px] bg-gray-50 text-gray-400 px-1.5 md:px-2 py-0.5 md:py-1 rounded-md font-bold flex items-center gap-1 w-fit border border-gray-100 uppercase tracking-tighter">
                                    <i class="fa-solid fa-file-circle-xmark"></i> KOSONG
                                </span>
                            @endif
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-1 md:gap-2">
                                <button onclick="openDetailModal({{ json_encode($tool) }})" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Detail">
                                    <i class="fa-solid fa-eye text-[8px] md:text-[10px]"></i>
                                </button>
                                <button onclick="openEditModal({{ json_encode($tool) }})" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-[8px] md:text-[10px]"></i>
                                </button>
                                <button onclick="confirmDelete('{{ $tool->id }}')" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Hapus">
                                    <i class="fa-solid fa-trash text-[8px] md:text-[10px]"></i>
                                </button>
                                <form id="delete-form-{{ $tool->id }}" action="{{ route('qcc.admin.delete_seven_tool', $tool->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-300 italic text-xs md:text-sm">Belum ada tool terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION AREA -->
        <div class="mt-4 md:mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-4 md:pt-6">
            <div class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $tools->firstItem() ?? 0 }} to {{ $tools->lastItem() ?? 0 }} of {{ $tools->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $tools->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div id="modalDetail" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-circle-info mr-2"></i> Detail Seven Tool</h3>
                <button onclick="closeModal('modalDetail')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <div class="p-4 md:p-8 space-y-4 md:space-y-6">
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Nama Tool</label>
                    <h2 id="det_name" class="text-lg md:text-2xl font-bold text-[#091E6E] leading-tight">-</h2>
                </div>
                <div class="bg-gray-50 p-4 md:p-6 rounded-xl md:rounded-2xl border border-gray-100 shadow-inner">
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 block">Deskripsi</label>
                    <p id="det_desc" class="text-gray-600 leading-relaxed italic text-xs md:text-sm">-</p>
                </div>
                <div id="det_file_area" class="hidden">
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 block">Template Master</label>
                    <a id="det_download_link" href="#" target="_blank" class="flex items-center gap-2 md:gap-3 p-3 md:p-4 bg-blue-50 border border-blue-100 rounded-lg md:rounded-xl hover:bg-blue-100 transition-all group">
                        <i class="fa-solid fa-file-export text-red-500 text-base md:text-xl group-hover:scale-110 transition-transform"></i>
                        <span id="det_file_name" class="text-[9px] md:text-xs font-bold text-[#091E6E] truncate">Download Template</span>
                    </a>
                </div>
                <button onclick="closeModal('modalDetail')" class="w-full py-3 md:py-4 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-[10px] md:text-xs hover:bg-gray-200 transition-all">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL ADD ================= -->
<div id="modalAdd" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">  
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-plus-circle mr-2"></i> Tambah Tool Baru</h3>
                <button onclick="closeModal('modalAdd')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <form id="formAdd" action="{{ route('qcc.admin.store_seven_tool') }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-8 space-y-4 md:space-y-6">
                @csrf
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Tool</label>
                    <input type="text" name="tool_name" required placeholder="Contoh: Fishbone Diagram" class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-medium text-xs md:text-sm">
                </div>
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan kegunaan tool ini..." class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-medium text-xs md:text-sm"></textarea>
                </div>
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Template File (PPT/Excel/PDF)</label>
                    <input type="file" name="template_file" class="w-full mt-1 md:mt-2 text-[9px] md:text-xs text-gray-400 file:mr-3 md:file:mr-4 file:py-1.5 md:file:py-2 file:px-3 md:file:px-4 file:rounded-full file:border-0 file:text-[9px] md:file:text-xs file:font-bold file:bg-blue-50 file:text-[#091E6E] hover:file:bg-blue-100">
                </div>
                <button type="submit" class="w-full py-3 md:py-4 bg-[#091E6E] text-white rounded-xl font-bold shadow-lg hover:bg-[#130998] transition-all uppercase tracking-widest text-[10px] md:text-xs">Simpan Tool</button>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2.5rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-pen-to-square mr-2"></i> Edit Seven Tool</h3>
                <button onclick="closeModal('modalEdit')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <form id="formEdit" method="POST" enctype="multipart/form-data" class="p-4 md:p-8 space-y-4 md:space-y-6">
                @csrf @method('PUT')
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Tool</label>
                    <input type="text" name="tool_name" id="edit_name" required class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-medium text-xs md:text-sm">
                </div>
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Deskripsi</label>
                    <textarea name="description" id="edit_desc" rows="3" class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-medium text-xs md:text-sm"></textarea>
                </div>
                <div class="border-t pt-3 md:pt-4">
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Ganti Template File</label>
                    <input type="file" name="template_file" class="w-full mt-1 md:mt-2 text-[9px] md:text-xs text-gray-400 file:mr-3 md:file:mr-4 file:py-1.5 md:file:py-2 file:px-3 md:file:px-4 file:rounded-full file:border-0 file:text-[9px] md:file:text-xs file:font-bold file:bg-amber-50 file:text-amber-600 hover:file:bg-amber-100">
                    <div id="current_file_info" class="mt-2 md:mt-3 hidden">
                        <p class="text-[8px] md:text-[10px] text-emerald-600 font-bold flex items-center gap-1 italic">
                            <i class="fa-solid fa-paperclip"></i> File Aktif: <span id="txt_file_name"></span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 md:gap-3 mt-2 md:mt-4">
                    <button type="button" onclick="closeModal('modalEdit')" class="flex-1 py-3 md:py-4 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-[9px] md:text-[10px]">Batal</button>
                    <button type="submit" class="flex-1 py-3 md:py-4 bg-amber-500 text-white rounded-xl font-bold shadow-lg hover:bg-amber-600 transition-all uppercase tracking-widest text-[9px] md:text-xs">Update Data</button>
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
    .custom-pagination nav svg { width: 0.875rem; height: 0.875rem; }
    @media (min-width: 768px) {
        .custom-pagination nav svg { width: 1rem; height: 1rem; }
    }
    .custom-pagination span[aria-current="page"] > span { 
        background-color: #091E6E !important; color: white !important; border: none !important;
        border-radius: 6px !important; padding: 4px 10px !important; font-size: 10px !important; font-weight: 800;
        box-shadow: 0 4px 6px -1px rgba(9, 30, 110, 0.2);
    }
    .custom-pagination a, .custom-pagination span { 
        border-radius: 6px !important; padding: 4px 10px !important; font-size: 10px !important;
        font-weight: 700 !important; border: 1px solid #edf2f7 !important; color: #64748b;
        transition: all 0.2s ease;
    }
    @media (min-width: 768px) {
        .custom-pagination span[aria-current="page"] > span { padding: 6px 12px !important; font-size: 11px !important; }
        .custom-pagination a, .custom-pagination span { padding: 6px 12px !important; font-size: 11px !important; }
    }
    .custom-pagination a:hover { background-color: #f8fafc !important; border-color: #091E6E !important; color: #091E6E !important; }
</style>

<script>
    // Fungsi dasar Modal
    function openModal(id) { 
        document.getElementById(id).classList.remove('hidden'); 
        document.body.style.overflow = 'hidden'; 
    }
    function closeModal(id) { 
        document.getElementById(id).classList.add('hidden'); 
        document.body.style.overflow = 'auto'; 
    }

    // Detail Modal
    function openDetailModal(tool) {
        document.getElementById('det_name').innerText = tool.tool_name;
        document.getElementById('det_desc').innerText = tool.description || 'Tidak ada deskripsi.';
        
        const fileArea = document.getElementById('det_file_area');
        if(tool.template_file_path) {
            fileArea.classList.remove('hidden');
            document.getElementById('det_download_link').href = `/storage/${tool.template_file_path}`;
            document.getElementById('det_file_name').innerText = tool.template_file_name;
        } else {
            fileArea.classList.add('hidden');
        }
        openModal('modalDetail');
    }

    // Edit Modal
    function openEditModal(tool) {
        let updateUrl = "{{ route('qcc.admin.update_seven_tool', ':id') }}";
        document.getElementById('formEdit').action = updateUrl.replace(':id', tool.id);
        document.getElementById('edit_name').value = tool.tool_name;
        document.getElementById('edit_desc').value = tool.description;
        
        const fileInfo = document.getElementById('current_file_info');
        if(tool.template_file_name) {
            fileInfo.classList.remove('hidden');
            document.getElementById('txt_file_name').innerText = tool.template_file_name;
        } else {
            fileInfo.classList.add('hidden');
        }
        openModal('modalEdit');
    }

    // === SWEETALERT HANDLING ===

    // 1. Success Message
    @if(Session::has('success')) 
        Swal.fire({ 
            icon: 'success', 
            title: 'Berhasil!', 
            text: "{{ Session::get('success') }}", 
            timer: 2500, 
            showConfirmButton: false, 
            background: '#ffffff', 
            iconColor: '#10B981', 
            customClass: { title: 'text-[#091E6E] font-bold' } 
        }); 
    @endif

    // 2. Error Message
    @if(Session::has('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ Session::get('error') }}",
            confirmButtonColor: '#091E6E',
            background: '#ffffff',
            iconColor: '#EF4444'
        });
    @endif

    // 3. Confirm Store (Tambah Tool)
    document.getElementById('formAdd').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ 
            title: 'Simpan Seven Tool?', 
            text: "Pastikan data tool sudah benar.", 
            icon: 'question', 
            showCancelButton: true, 
            confirmButtonColor: '#091E6E', 
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Simpan!' 
        }).then((result) => { 
            if (result.isConfirmed) this.submit(); 
        });
    });

    // 4. Confirm Update (Edit Tool)
    document.getElementById('formEdit').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ 
            title: 'Update Data Tool?', 
            text: "Perubahan data dan file template akan disimpan.", 
            icon: 'question', 
            showCancelButton: true, 
            confirmButtonColor: '#F59E0B', 
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Update!' 
        }).then((result) => { 
            if (result.isConfirmed) this.submit(); 
        });
    });

    // 5. Confirm Delete (Hapus Tool)
    function confirmDelete(id) {
        Swal.fire({ 
            title: 'Hapus Seven Tool?', 
            text: "Data yang dihapus tidak bisa dikembalikan!", 
            icon: 'warning', 
            showCancelButton: true, 
            confirmButtonColor: '#EF4444', 
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!' 
        }).then((result) => { 
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit(); 
            }
        })
    }
</script>
@endpush
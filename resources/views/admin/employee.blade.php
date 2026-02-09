@extends('welcome')

@section('title', 'Master Data Karyawan')

@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <i class="fa-solid fa-database mr-2 text-xs"></i> Master
            </li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight">Data Karyawan</li>
        </ol>
    </nav>

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Master Karyawan</h2>
            <p class="text-sm text-gray-400">Manajemen database personil PT Aisin Indonesia Automotive</p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-end items-center">
            <!-- Form Filter -->
            <form action="{{ route('admin.master_employee.index') }}" method="GET" id="filterForm" class="flex items-center gap-3">
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NPK..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm transition-all text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </form>

            <button onclick="openModal('modalAdd')" class="bg-[#091E6E] hover:bg-[#130998] text-white px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg transition-all active:scale-95 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-user-plus"></i> Tambah Karyawan
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[2rem] p-6 shadow-sm border border-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl">Karyawan</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold">Jabatan</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold text-center">Sub-Section</th>
                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all group shadow-sm border border-gray-100">
                        <td class="px-6 py-3 rounded-l-xl border-y border-l border-gray-100">
                            <p class="font-bold text-[#091E6E] text-sm group-hover:text-[#130998]">{{ $emp->nama }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">NPK: {{ $emp->npk }}</p>
                        </td>
                        <td class="px-6 py-3 border-y border-gray-100">
                            <span class="text-gray-600 text-xs font-semibold">{{ $emp->job->name ?? $emp->occupation }}</span>
                        </td>
                        <td class="px-6 py-3 border-y border-gray-100 text-center">
                            <span class="text-[10px] bg-gray-50 text-gray-500 px-3 py-1 rounded-lg font-bold border border-gray-100">{{ $emp->sub_section }}</span>
                        </td>
                        <td class="px-6 py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick="openDetailModal({{ json_encode($emp) }})" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-eye text-[10px]"></i>
                                </button>
                                <button onclick="openEditModal({{ json_encode($emp) }})" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </button>
                                <button onclick="confirmDelete('{{ $emp->id }}', '{{ $emp->nama }}')" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                                <form id="delete-form-{{ $emp->id }}" action="{{ route('admin.master_employee.destroy', $emp->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-20">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <i class="fa-solid fa-folder-open text-4xl"></i>
                                <span class="italic text-sm">Data karyawan tidak ditemukan...</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-6">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $employees->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div id="modalDetail" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fa-solid fa-id-card mr-2"></i>
                    Profil Karyawan
                </h3>
                <button onclick="closeModal('modalDetail')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8 space-y-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center text-3xl font-black text-[#091E6E] border border-blue-100 shadow-inner" id="det_avatar">
                        ?
                    </div>
                    <div>
                        <h4 id="det_nama" class="text-2xl font-bold text-[#091E6E] leading-tight"></h4>
                        <p id="det_npk" class="text-sm text-gray-400 font-bold uppercase tracking-widest"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-6 rounded-2xl border border-gray-100 shadow-inner font-medium">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jabatan</label>
                        <p id="det_occupation" class="text-[#091E6E] font-bold"></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dept / Line</label>
                        <p id="det_line" class="text-[#091E6E] font-bold"></p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sub-Section</label>
                        <p id="det_sub" class="text-[#091E6E] font-bold"></p>
                    </div>
                </div>
                <button onclick="closeModal('modalDetail')" class="w-full py-4 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-gray-200 transition-all">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL ADD ================= -->
<div id="modalAdd" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-xl shadow-2xl animate-reveal overflow-hidden">  
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fa-solid fa-user-plus mr-2"></i>
                    Registrasi Karyawan
                </h3>
                <button onclick="closeModal('modalAdd')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="formAdd" action="{{ route('admin.master_employee.store') }}" method="POST" class="p-8 grid grid-cols-2 gap-5 text-left">
                @csrf
                <div class="col-span-2 text-left">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="Masukkan nama lengkap" class="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none transition-all font-medium text-[#091E6E]">
                </div>
                <div>
                    <!-- NPK READONLY DAN OTOMATIS -->
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">NPK (Otomatis)</label>
                    <input type="text" name="npk" value="{{ $nextNpk }}" readonly class="w-full mt-1 px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl outline-none font-black text-[#091E6E] cursor-not-allowed shadow-inner">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Jabatan</label>
                    <select name="occupation" required class="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-[#091E6E]">
                        @foreach($occupations as $occ) <option value="{{ $occ->code }}">{{ $occ->name }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Dept / Line Code</label>
                    <input type="text" name="line_code" required placeholder="Contoh: PROD1" class="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-medium text-[#091E6E]">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Sub-Section</label>
                    <select name="sub_section" required class="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-[#091E6E]">
                        @foreach($subSections as $sub) <option value="{{ $sub->code }}">{{ $sub->code }} - {{ $sub->name }}</option> @endforeach
                    </select>
                </div>
                <button type="submit" class="col-span-2 py-4 bg-[#091E6E] text-white rounded-xl font-bold shadow-lg hover:bg-[#130998] transition-all uppercase tracking-widest text-xs active:scale-95">Simpan Data Karyawan</button>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2.5rem] w-full max-w-xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold text-white"><i class="fa-solid fa-pen-to-square mr-2"></i>
                    Update Data Karyawan
                </h3>
                <button onclick="closeModal('modalEdit')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="formEdit" method="POST" class="p-8 grid grid-cols-2 gap-5 text-left">
                @csrf @method('PUT')
                <div class="col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                    <input type="text" name="nama" id="edit_nama" required class="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-medium text-[#091E6E]">
                </div>
                <div>
                    <!-- NPK READONLY PADA MODAL EDIT -->
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">NPK (Permanen)</label>
                    <input type="text" name="npk" id="edit_npk" readonly class="w-full mt-1 px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl outline-none font-black text-[#091E6E] cursor-not-allowed shadow-inner">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Jabatan</label>
                    <select name="occupation" id="edit_occupation" required class="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-[#091E6E]">
                        @foreach($occupations as $occ) <option value="{{ $occ->code }}">{{ $occ->name }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Dept / Line Code</label>
                    <input type="text" name="line_code" id="edit_line_code" required class="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Sub-Section</label>
                    <select name="sub_section" id="edit_sub_section" required class="w-full mt-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-[#091E6E]">
                        @foreach($subSections as $sub) <option value="{{ $sub->code }}">{{ $sub->code }} - {{ $sub->name }}</option> @endforeach
                    </select>
                </div>
                <div class="flex gap-3 col-span-2 mt-4">
                    <button type="button" onclick="closeModal('modalEdit')" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-[10px] rounded-xl hover:bg-gray-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-amber-500 text-white rounded-xl font-bold shadow-lg uppercase tracking-widest text-[10px] hover:bg-amber-600 transition-all active:scale-95">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Pagination Styling */
    .custom-pagination nav { display: flex; align-items: center; justify-content: center; gap: 4px; }
    .custom-pagination nav svg { width: 1.25rem; height: 1.25rem; }
    .custom-pagination span[aria-current="page"] > span { 
        background-color: #091E6E !important; 
        color: white !important; 
        border: none !important;
        border-radius: 0.75rem; 
        padding: 6px 14px !important;
        font-size: 11px !important;
        font-weight: 800;
    }
    .custom-pagination a, .custom-pagination span { 
        border-radius: 0.75rem !important; 
        padding: 6px 14px !important; 
        font-size: 11px !important;
        font-weight: 700 !important;
        border: 1px solid #edf2f7 !important;
        color: #64748b;
        transition: all 0.2s ease;
    }
</style>

<script>
    function openModal(id) { 
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden'); 
            document.body.style.overflow = 'hidden'; 
        }
    }
    function closeModal(id) { 
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden'); 
            document.body.style.overflow = 'auto'; 
        }
    }

    // --- FUNGSI DETAIL ---
    function openDetailModal(emp) {
        document.getElementById('det_nama').innerText = emp.nama;
        document.getElementById('det_npk').innerText = "NPK: " + emp.npk;
        document.getElementById('det_avatar').innerText = emp.nama.charAt(0);
        document.getElementById('det_occupation').innerText = emp.job?.name || emp.occupation;
        document.getElementById('det_line').innerText = emp.line_code;
        document.getElementById('det_sub').innerText = emp.sub_section;
        openModal('modalDetail');
    }

    // --- FUNGSI EDIT ---
    function openEditModal(emp) {
        // Gunakan route name yang konsisten dengan web.php
        document.getElementById('formEdit').action = `/admin/master-employee/${emp.id}`;
        document.getElementById('edit_nama').value = emp.nama;
        document.getElementById('edit_npk').value = emp.npk;
        document.getElementById('edit_occupation').value = emp.occupation;
        document.getElementById('edit_line_code').value = emp.line_code;
        document.getElementById('edit_sub_section').value = emp.sub_section;
        openModal('modalEdit');
    }

    // --- FUNGSI DELETE ---
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Karyawan?',
            html: `Apakah Anda yakin ingin menghapus <b>${name}</b>?<br><small class="text-red-500 italic">Data yang berhubungan dengan QCC/SS orang ini mungkin terpengaruh.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => { 
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit(); 
            }
        });
    }

    // --- KONFIRMASI SIMPAN ---
    document.getElementById('formAdd').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ 
            title: 'Simpan Data?', 
            text: "NPK akan digenerate otomatis.", 
            icon: 'question', 
            showCancelButton: true, 
            confirmButtonColor: '#091E6E', 
            confirmButtonText: 'Ya, Simpan!' 
        }).then((result) => { 
            if (result.isConfirmed) this.submit(); 
        });
    });

    // --- KONFIRMASI UPDATE ---
    document.getElementById('formEdit').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ 
            title: 'Update Data?', 
            text: "Perubahan akan disimpan secara permanen.", 
            icon: 'question', 
            showCancelButton: true, 
            confirmButtonColor: '#F59E0B', 
            confirmButtonText: 'Ya, Update!' 
        }).then((result) => { 
            if (result.isConfirmed) this.submit(); 
        });
    });

    // Handle Global Alerts
    @if(Session::has('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ Session::get('success') }}", timer: 2000, showConfirmButton: false });
    @endif
</script>
@endpush
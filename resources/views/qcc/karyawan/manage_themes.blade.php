@extends('welcome')
@section('title', 'Manajemen Tema QCC')
@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li><a href="{{ route('qcc.karyawan.my_circle') }}" class="hover:text-[#091E6E]">Circle Saya</a></li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight">Manajemen Tema</li>
        </ol>
    </nav>

    <!-- Header & Search Controls -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end mb-8 gap-6">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Daftar Tema Circle</h2>
            <p class="text-sm text-gray-400">Circle: <span class="text-[#1035D1] font-black uppercase tracking-wider">{{ $circle->circle_name }}</span></p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto justify-end">
            <!-- Form Filter -->
            <form action="{{ route('qcc.karyawan.themes') }}" method="GET" id="filterForm" class="flex items-center gap-3 flex-1 xl:flex-none">
                <input type="hidden" name="circle_id" value="{{ $circle->id }}">
                
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E]">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                
                <div class="relative flex-1 md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul tema..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm transition-all text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </form>

            <div class="flex gap-2">
                <a href="{{ route('qcc.karyawan.my_circle') }}" class="bg-gray-100 text-gray-500 px-5 py-2 rounded-xl font-bold hover:bg-gray-200 transition-all text-xs flex items-center gap-2 shadow-sm uppercase tracking-widest">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
                <button onclick="openModal('modalAddTheme')" class="bg-[#091E6E] hover:bg-[#130998] text-white px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg transition-all active:scale-95 text-xs font-bold uppercase tracking-widest">
                    <i class="fa-solid fa-plus"></i> Tambah Tema
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[2rem] p-6 shadow-sm border border-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl">Periode</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold">Judul Tema</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold text-center">Status</th>
                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($themes as $theme)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100 group">
                        <td class="px-6 py-4 rounded-l-xl border-y border-l">
                            <span class="font-bold text-[#091E6E] text-xs">{{ $theme->period->period_name }}</span>
                        </td>
                        <td class="px-6 py-4 border-y">
                            <p class="font-bold text-[#091E6E] group-hover:text-[#1035D1] transition-colors">{{ $theme->theme_name }}</p>
                        </td>
                        <td class="px-6 py-4 border-y text-center">
                            <span class="px-3 py-1 rounded-full text-[9px] font-bold border {{ $theme->status == 'ACTIVE' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gray-50 text-gray-400 border-gray-100' }}">
                                {{ $theme->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 rounded-r-xl border-y border-r text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('qcc.karyawan.progress') }}?theme_id={{ $theme->id }}" class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-lg font-bold text-[9px] uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    Progress
                                </a>
                                <button onclick="openEditThemeModal({{ json_encode($theme) }})" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </button>
                                <button onclick="confirmDeleteTheme('{{ $theme->id }}', '{{ $theme->theme_name }}')" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                                <form id="delete-theme-{{ $theme->id }}" action="#" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-10">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <i class="fa-solid fa-folder-open text-4xl"></i>
                                <span class="italic text-sm font-medium">Belum ada tema yang didaftarkan.</span>
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
                Showing {{ $themes->firstItem() ?? 0 }} to {{ $themes->lastItem() ?? 0 }} of {{ $themes->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $themes->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL ADD THEME ================= -->
<div id="modalAddTheme" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden border border-white">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold uppercase tracking-widest">Tambah Tema Baru</h3>
                <button onclick="closeModal('modalAddTheme')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="formStoreTheme" action="{{ route('qcc.karyawan.store_theme') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="qcc_circle_id" value="{{ $circle->id }}">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Pilih Periode QCC</label>
                    <select name="qcc_period_id" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-[#091E6E] focus:ring-2 focus:ring-[#091E6E]">
                        <option value="">Pilih Periode Aktif</option>
                        @foreach($activePeriods as $p) 
                            <option value="{{ $p->id }}">{{ $p->period_name }} ({{ $p->year }})</option> 
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Judul Tema Perbaikan</label>
                    <input type="text" name="theme_name" required placeholder="Masukkan judul tema..." class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-medium">
                </div>
                <button type="submit" class="w-full py-4 bg-[#091E6E] text-white rounded-xl font-bold shadow-lg uppercase tracking-widest text-xs">Simpan Tema</button>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT THEME ================= -->
<div id="modalEditTheme" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden border border-white">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold uppercase tracking-widest text-white">Edit Tema QCC</h3>
                <button onclick="closeModal('modalEditTheme')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="formUpdateTheme" method="POST" class="p-8 space-y-6">
                @csrf @method('PUT')
                <input type="hidden" name="qcc_circle_id" value="{{ $circle->id }}">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Pilih Periode</label>
                    <select name="qcc_period_id" id="edit_theme_period" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                        @foreach($activePeriods as $p) <option value="{{ $p->id }}">{{ $p->period_name }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Judul Tema</label>
                    <input type="text" name="theme_name" id="edit_theme_name" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-medium">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('modalEditTheme')" class="flex-1 py-4 bg-gray-100 text-gray-400 font-bold uppercase tracking-widest text-xs rounded-xl transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-amber-500 text-white rounded-xl font-bold shadow-lg uppercase tracking-widest text-xs hover:bg-amber-600 transition-all">Update Tema</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    .custom-pagination nav { display: flex; align-items: center; justify-content: center; gap: 4px; }
    .custom-pagination nav svg { width: 1rem; height: 1rem; }
    .custom-pagination span[aria-current="page"] > span { 
        background-color: #091E6E !important; color: white !important; border: none !important;
        border-radius: 8px !important; padding: 6px 12px !important; font-size: 11px !important; font-weight: 800;
    }
    .custom-pagination a, .custom-pagination span { 
        border-radius: 8px !important; padding: 6px 12px !important; font-size: 11px !important;
        font-weight: 700 !important; border: 1px solid #edf2f7 !important; color: #64748b;
    }
</style>

<script>
    function openModal(id) { 
        const modal = document.getElementById(id);
        if (modal) { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    }
    function closeModal(id) { 
        const modal = document.getElementById(id);
        if (modal) { modal.classList.add('hidden'); document.body.style.overflow = 'auto'; }
    }

    function openEditThemeModal(theme) {
        document.getElementById('formUpdateTheme').action = `/qcc/karyawan/update-theme/${theme.id}`;
        document.getElementById('edit_theme_name').value = theme.theme_name;
        document.getElementById('edit_theme_period').value = theme.qcc_period_id;
        openModal('modalEditTheme');
    }

    function confirmDeleteTheme(id, name) {
        Swal.fire({
            title: 'Hapus Tema?',
            html: `Apakah Anda yakin ingin menghapus tema <b>${name}</b>?<br><small class="text-red-500">Seluruh file progres terkait akan terhapus!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => { if (result.isConfirmed) { /* submit delete form logic */ } });
    }

    // SweetAlert handling (Success, Confirm Store) ...
    @if(Session::has('success')) Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ Session::get('success') }}", timer: 2500, showConfirmButton: false }); @endif
</script>
@endpush
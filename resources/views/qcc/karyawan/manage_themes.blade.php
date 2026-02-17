@extends('welcome')

@section('title', 'Manajemen Tema QCC')

@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <i class="fa-solid fa-house mr-2 text-xs"></i> Monitoring QCC
            </li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li><a href="{{ route('qcc.karyawan.my_circle') }}" class="hover:text-[#091E6E] transition-colors">Circle Saya</a></li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight">Manajemen Tema</li>
        </ol>
    </nav>

    <!-- Header & Search Controls -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end mb-8 gap-6">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Daftar Tema Circle</h2>
            <!-- Bagian ini diubah menjadi dropdown atau info circle -->
            <p class="text-sm text-gray-400">Kelola tema perbaikan untuk circle terpilih</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto justify-end">
            <!-- Form Filter: Search & Per Page -->
            <form action="{{ route('qcc.karyawan.themes') }}" method="GET" id="filterForm" class="flex flex-wrap items-center gap-3 flex-1 xl:flex-none">
                
                <!-- NEW: Dropdown Pilih Circle -->
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E]">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pilih Circle</span>
                    <select name="circle_id" onchange="this.form.submit()" class="text-xs font-bold text-[#1035D1] outline-none bg-transparent cursor-pointer uppercase">
                        @foreach($myCircles as $c)
                            <option value="{{ $c->id }}" {{ $circle->id == $c->id ? 'selected' : '' }}>
                                {{ $c->circle_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Show Entries -->
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E]">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                
                <!-- Search Input -->
                <div class="relative flex-1 md:min-w-[250px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul tema..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm transition-all text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </form>

            <div class="flex gap-2">
                <a href="{{ route('qcc.karyawan.my_circle') }}" class="bg-gray-100 text-gray-500 px-5 py-2 rounded-xl font-bold hover:bg-gray-200 transition-all text-xs flex items-center gap-2 shadow-sm uppercase tracking-widest active:scale-95">
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
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl w-1/4">Periode</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold">Judul Tema Perbaikan</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold text-center w-32">Status</th>
                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl w-48">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($themes as $theme)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100 group">
                        <td class="px-6 py-4 rounded-l-xl border-y border-l">
                            <span class="font-bold text-[#091E6E] text-xs uppercase">{{ $theme->period->period_name }}</span>
                            <p class="text-[9px] text-gray-400 font-bold">Tahun: {{ $theme->period->year }}</p>
                        </td>
                        <td class="px-6 py-4 border-y">
                            <p class="font-bold text-[#091E6E] group-hover:text-[#1035D1] transition-colors leading-tight">{{ $theme->theme_name }}</p>
                        </td>
                        <td class="px-6 py-4 border-y text-center">
                            <span class="px-3 py-1 rounded-full text-[9px] font-bold border {{ $theme->status == 'ACTIVE' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gray-50 text-gray-400 border-gray-100' }}">
                                {{ $theme->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 rounded-r-xl border-y border-r text-center">
                            <div class="flex justify-center gap-2">
                                <!-- Ke Progress Transaksi -->
                                <a href="{{ route('qcc.karyawan.progress') }}?theme_id={{ $theme->id }}" class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg font-bold text-[9px] uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-95 flex items-center gap-1">
                                    <i class="fa-solid fa-spinner"></i> Progress
                                </a>
                                <!-- Edit -->
                                <button onclick="openEditThemeModal({{ json_encode($theme) }})" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm active:scale-95" title="Edit Tema">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </button>
                                <!-- Hapus -->
                                <button onclick="confirmDeleteTheme('{{ $theme->id }}', '{{ $theme->theme_name }}')" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm active:scale-95" title="Hapus Tema">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                                <form id="delete-theme-{{ $theme->id }}" action="{{ route('qcc.karyawan.delete_theme', $theme->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-20">
                            <div class="flex flex-col items-center gap-3 text-gray-300">
                                <i class="fa-solid fa-folder-open text-5xl"></i>
                                <span class="italic text-sm font-medium uppercase tracking-widest">Belum ada tema QCC terdaftar</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION FOOTER -->
        <div class="mt-8 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-6">
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
<div id="modalAddTheme" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm transition-all">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-lightbulb mr-2"></i>
                    Tambah Tema QCC
                </h3>
                <button onclick="closeModal('modalAddTheme')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20 transition-all text-xl">&times;</button>
            </div>
            <form id="formStoreTheme" action="{{ route('qcc.karyawan.store_theme') }}" method="POST" class="p-10 space-y-6 text-left">
                @csrf
                <input type="hidden" name="qcc_circle_id" value="{{ $circle->id }}">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Pilih Periode Aktif</label>
                    <select name="qcc_period_id" required class="w-full mt-2 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl outline-none font-bold text-[#091E6E] focus:ring-2 focus:ring-[#091E6E] transition-all">
                        <option value="">-- Pilih Periode --</option>
                        @foreach($activePeriods as $p) 
                            <option value="{{ $p->id }}">{{ $p->period_name }} ({{ $p->year }})</option> 
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Judul Tema Perbaikan</label>
                    <input type="text" name="theme_name" required placeholder="Contoh: Optimasi Efisiensi Mesin CNC-01" class="w-full mt-2 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#091E6E] outline-none font-medium text-[#091E6E] transition-all">
                </div>
                <div class="bg-blue-50 p-5 rounded-2xl border border-blue-100 text-[10px] text-blue-600 italic">
                    <i class="fa-solid fa-circle-info mr-1 text-sm"></i> Tema yang ditambahkan akan otomatis berstatus <b>ACTIVE</b> dan dapat diupdate progres PDCA-nya.
                </div>
                <button type="submit" class="w-full py-4 bg-[#091E6E] text-white rounded-2xl font-bold shadow-lg hover:bg-[#130998] transition-all uppercase tracking-widest text-xs">Simpan Data Tema</button>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT THEME ================= -->
<div id="modalEditTheme" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm transition-all">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-pen-to-square mr-2"></i>
                    Update Data Tema
                </h3>
                <button onclick="closeModal('modalEditTheme')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20 transition-all text-xl">&times;</button>
            </div>
            <form id="formUpdateTheme" method="POST" class="p-10 space-y-6 text-left">
                @csrf @method('PUT')
                <input type="hidden" name="qcc_circle_id" value="{{ $circle->id }}">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Pilih Periode</label>
                    <select name="qcc_period_id" id="edit_theme_period" required class="w-full mt-2 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-bold text-[#091E6E] transition-all">
                        @foreach($activePeriods as $p) <option value="{{ $p->id }}">{{ $p->period_name }} ({{ $p->year }})</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Judul Tema</label>
                    <input type="text" name="theme_name" id="edit_theme_name" required class="w-full mt-2 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none font-medium text-[#091E6E] transition-all">
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeModal('modalEditTheme')" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold uppercase tracking-widest text-[10px] hover:bg-gray-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-amber-500 text-white rounded-2xl font-bold shadow-lg uppercase tracking-widest text-[10px] hover:bg-amber-600 transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Styling Paging Agar Senada */
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
    // --- FUNGSI MODAL DASAR ---
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

    // --- FUNGSI EDIT TEMA ---
    function openEditThemeModal(theme) {
        // Set action form secara dinamis untuk menghindari 404
        document.getElementById('formUpdateTheme').action = `/qcc/karyawan/update-theme/${theme.id}`;
        document.getElementById('edit_theme_name').value = theme.theme_name;
        document.getElementById('edit_theme_period').value = theme.qcc_period_id;
        openModal('modalEditTheme');
    }

    // --- FUNGSI DELETE TEMA ---
    function confirmDeleteTheme(id, name) {
        Swal.fire({
            title: 'Hapus Tema?',
            html: `Apakah Anda yakin ingin menghapus tema:<br><b>${name}</b>?<br><small class="text-red-500 italic">*Seluruh data progres PDCA pada tema ini akan ikut terhapus.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus Sekarang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-theme-' + id).submit();
            }
        });
    }

    // --- KONFIRMASI SIMPAN TEMA BARU ---
    document.getElementById('formStoreTheme')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Simpan Tema Baru?',
            text: "Pastikan judul tema dan periode sudah sesuai standar PDCA.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#091E6E',
            confirmButtonText: 'Ya, Simpan!'
        }).then((result) => { if (result.isConfirmed) this.submit(); });
    });

    // --- KONFIRMASI UPDATE TEMA ---
    document.getElementById('formUpdateTheme')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Data tema akan diperbarui secara permanen.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#F59E0B',
            confirmButtonText: 'Ya, Update!'
        }).then((result) => { if (result.isConfirmed) this.submit(); });
    });

    // --- GLOBAL ALERT HANDLER ---
    @if(Session::has('success'))
        Swal.fire({ 
            icon: 'success', title: 'Berhasil!', text: "{{ Session::get('success') }}", 
            timer: 2500, showConfirmButton: false, background: '#ffffff',
            iconColor: '#10B981', customClass: { title: 'text-[#091E6E] font-bold' }
        });
    @endif

    @if(Session::has('error'))
        Swal.fire({ 
            icon: 'error', title: 'Gagal!', text: "{{ Session::get('error') }}", 
            confirmButtonColor: '#091E6E' 
        });
    @endif
</script>
@endpush
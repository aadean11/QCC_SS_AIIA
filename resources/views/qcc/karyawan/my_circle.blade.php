@extends('welcome')
@section('title', 'Circle QCC Saya')
@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight">Circle QCC Saya</li>
        </ol>
    </nav>

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Daftar Circle QCC Saya</h2>
            <p class="text-sm text-gray-400">
                Departemen Anda: 
                <span class="font-bold text-[#1035D1]">
                    {{ $user->getDepartment()->name ?? $user->getDeptCode() }}
                </span>
            </p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-end items-center">
            <!-- Form Per Page & Search -->
            <form action="{{ route('qcc.karyawan.my_circle') }}" method="GET" id="filterForm" class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E]">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode circle..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm transition-all text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </form>

            <button onclick="openModal('modalCreateCircle')" class="bg-[#091E6E] hover:bg-[#130998] text-white px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg transition-all active:scale-95 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-plus"></i> Buat Circle
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[2rem] p-6 shadow-sm border border-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <!-- Tambah Kolom No -->
                        <th class="px-4 py-4 text-white text-[10px] uppercase tracking-widest font-bold rounded-tl-2xl text-center w-12">No</th>
                        <!-- Hapus rounded-tl-2xl dari Nama Circle -->
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold">Nama Circle</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold">Departemen</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold text-center">Status</th>
                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($circles as $c)
                    <tr class="bg-white hover:shadow-md transition-all group shadow-sm border border-gray-100">
                        <!-- Kolom Nomor Urut Otomatis -->
                        <td class="px-4 py-3 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500">
                            {{ ($circles->currentPage() - 1) * $circles->perPage() + $loop->iteration }}
                        </td>

                        <!-- Kolom Info Circle (Hapus rounded-l-xl dan border-l karena pindah ke No) -->
                        <td class="px-6 py-3 border-y border-gray-100">
                            <p class="font-bold text-[#091E6E] text-sm group-hover:text-[#130998]">{{ $c->circle_name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $c->circle_code }}</p>
                        </td>

                        <td class="px-6 py-3 border-y border-gray-100 text-gray-600 text-xs font-medium">
                            {{ $c->department->name ?? $c->department_code }}
                        </td>

                        <td class="px-6 py-3 border-y border-gray-100 text-center">
                            @php
                                $badgeColor = 'bg-amber-50 text-amber-600 border-amber-100';
                                if($c->status == 'ACTIVE') $badgeColor = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                if(str_contains($c->status, 'REJECTED')) $badgeColor = 'bg-red-50 text-red-600 border-red-100';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[9px] font-bold border {{ $badgeColor }}">
                                {{ $c->status }}
                            </span>
                        </td>

                        <td class="px-6 py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('qcc.karyawan.themes', ['circle_id' => $c->id]) }}" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Kelola Tema">
                                    <i class="fa-solid fa-lightbulb text-[10px]"></i>
                                </a>
                                <button onclick="openDetailCircle({{ json_encode($c->load('members.employee')) }})" class="w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-500 rounded-lg hover:bg-gray-200 transition-all shadow-sm" title="Detail Member">
                                    <i class="fa-solid fa-users text-[10px]"></i>
                                </button>
                                <button onclick="openEditCircle({{ json_encode($c) }}, {{ json_encode($c->members->pluck('employee_npk')) }})" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Edit Circle">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </button>
                                <button onclick="confirmDeleteCircle('{{ $c->id }}', '{{ $c->circle_name }}')" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Hapus Circle">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                                <form id="delete-circle-{{ $c->id }}" action="{{ route('qcc.karyawan.delete_circle', $c->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <!-- Update colspan dari 4 menjadi 5 -->
                        <td colspan="5" class="text-center py-10 text-gray-300 italic text-sm">Anda belum bergabung di circle manapun.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION AREA -->
        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-6">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $circles->firstItem() ?? 0 }} to {{ $circles->lastItem() ?? 0 }} of {{ $circles->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $circles->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL MEMBER ================= -->
<div id="modalDetailCircle" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-2xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fa-solid fa-users-rectangle mr-2"></i>
                    Detail & Anggota Kelompok
                </h3>
                <button onclick="closeModal('modalDetailCircle')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8">
                <div class="mb-6">
                    <h4 id="detailCircleName" class="text-2xl font-black text-[#091E6E]"></h4>
                    <p id="detailCircleCode" class="text-xs text-gray-400 font-bold uppercase tracking-widest"></p>
                </div>
                <div id="memberContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4 h-64 overflow-y-auto pr-2 custom-scrollbar">
                    <!-- Data member injected via JS -->
                </div>
                <div class="mt-8 pt-6 border-t">
                    <button onclick="closeModal('modalDetailCircle')" class="w-full py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold uppercase tracking-widest text-xs hover:bg-gray-200 transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL CREATE CIRCLE ================= -->
<div id="modalCreateCircle" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2.5rem] w-full max-w-4xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fa-solid fa-user-group mr-2"></i>
                    Buat Circle QCC Baru
                </h3>
                <button onclick="closeModal('modalCreateCircle')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>

            <form id="formStoreCircle" action="{{ route('qcc.karyawan.store_circle') }}" method="POST" class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                @csrf
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-[#091E6E] uppercase border-b pb-2">1. Detail Kelompok</h4>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Circle</label>
                        <input type="text" name="circle_name" required placeholder="Masukkan nama unik..." class="w-full mt-2 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#091E6E] outline-none font-medium text-[#091E6E]">
                    </div>

                    <!-- BOX INFORMASI HIERARKI USER -->
                    <div class="bg-blue-50/50 p-5 rounded-[1.5rem] border border-blue-100 relative overflow-hidden">
                        <p class="text-[9px] font-bold text-blue-400 uppercase mb-3 tracking-widest italic">Lingkup Area Rekan Kerja</p>
                        <div class="space-y-1 relative z-10 text-[10px] font-bold text-[#091E6E]">
                            <p class="uppercase italic">{{ $user->getDepartment()->name ?? 'DEPARTEMEN N/A' }}</p>
                            @if($user->occupation !== 'KDP')
                                <p class="text-blue-700">Section: {{ $user->subSection->section->name ?? ($user->section->name ?? 'N/A') }}</p>
                                <p class="text-blue-500">Sub Section: {{ $user->subSection->name ?? 'N/A' }}</p>
                            @else
                                <p class="text-blue-700 font-black">OTORITAS: KEPALA DEPARTEMEN</p>
                            @endif
                        </div>
                        <i class="fa-solid fa-sitemap absolute -right-2 -bottom-2 text-5xl text-blue-100 opacity-50"></i>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h4 class="text-xs font-bold text-[#091E6E] uppercase border-b pb-2 flex-1">2. Pilih Anggota Satu Departemen</h4>
                    </div>
                    
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="memberSearchCreate" placeholder="Cari Nama atau NPK rekan..." class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#091E6E] outline-none">
                    </div>

                    <div class="h-64 overflow-y-auto bg-gray-50 p-3 rounded-3xl border border-gray-200 space-y-2 custom-scrollbar">
                        @forelse($colleagues as $col)
                        <label class="member-card-create flex items-center gap-4 p-3 bg-white rounded-2xl cursor-pointer border-2 border-transparent hover:border-blue-200 transition-all group shadow-sm">
                            <input type="checkbox" name="members[]" value="{{ $col->npk }}" class="w-5 h-5 rounded border-gray-300 text-[#091E6E] focus:ring-[#091E6E]">
                            <div class="flex flex-col">
                                <span class="member-name text-xs font-bold text-gray-700 group-hover:text-[#091E6E]">{{ $col->nama }}</span>
                                <span class="member-npk text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $col->npk }}</span>
                            </div>
                        </label>
                        @empty
                        <p class="text-center text-gray-400 text-[10px] italic py-10">Tidak ada rekan dalam Departemen yang sama.</p>
                        @endforelse
                    </div>
                </div>

                <div class="md:col-span-2 pt-4 border-t">
                    <button type="submit" class="w-full py-4 bg-[#091E6E] text-white rounded-2xl font-bold shadow-lg hover:bg-[#130998] transition-all uppercase tracking-widest text-xs active:scale-95">Konfirmasi & Daftarkan Circle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT CIRCLE ================= -->
<div id="modalEditCircle" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2.5rem] w-full max-w-4xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fa-solid fa-pen-to-square mr-2"></i>
                    Update Data Circle
                </h3>
                <button onclick="closeModal('modalEditCircle')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="formUpdateCircle" method="POST" class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                @csrf @method('PUT')
                <div class="space-y-6">
                    <h4 class="text-xs font-bold text-[#091E6E] uppercase border-b pb-2">Detail Kelompok</h4>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Circle</label>
                        <input type="text" name="circle_name" id="edit_circle_name" required class="w-full mt-2 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                </div>
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-[#091E6E] uppercase border-b pb-2">Pilih Ulang Anggota</h4>
                    
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="memberSearchEdit" placeholder="Cari Nama atau NPK rekan..." class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>

                    <div class="h-64 overflow-y-auto bg-gray-50 p-3 rounded-3xl border border-gray-200 space-y-2 custom-scrollbar">
                        @foreach($colleagues as $col)
                        <label class="member-card-edit flex items-center gap-4 p-3 bg-white rounded-2xl cursor-pointer border-2 border-transparent hover:border-amber-200 transition-all group shadow-sm">
                            <input type="checkbox" name="members[]" value="{{ $col->npk }}" class="edit-member-checkbox w-5 h-5 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                            <div class="flex flex-col">
                                <span class="member-name text-xs font-bold text-gray-700">{{ $col->nama }}</span>
                                <span class="member-npk text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $col->npk }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="md:col-span-2 pt-4 border-t flex gap-3">
                    <button type="button" onclick="closeModal('modalEditCircle')" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-xs">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-amber-500 text-white rounded-xl font-bold shadow-lg hover:bg-amber-600 transition-all uppercase tracking-widest text-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SCRIPT TETAP SAMA SEPERTI SEBELUMNYA --}}
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

    function openDetailCircle(circle) {
        document.getElementById('detailCircleName').innerText = circle.circle_name;
        document.getElementById('detailCircleCode').innerText = "Circle Code: " + circle.circle_code;
        const container = document.getElementById('memberContainer');
        container.innerHTML = ''; 

        circle.members.forEach(m => {
            const isLeader = m.role === 'LEADER';
            const badgeClass = isLeader ? 'bg-amber-100 text-amber-600 border-amber-200' : 'bg-blue-100 text-blue-600 border-blue-200';
            container.innerHTML += `
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-[#091E6E] font-black shadow-sm text-lg border border-blue-50">
                        ${(m.employee?.nama || '?').charAt(0)}
                    </div>
                    <div class="flex-1 leading-tight">
                        <p class="text-sm font-bold text-[#091E6E]">${m.employee?.nama || 'Unknown'}</p>
                        <p class="text-[10px] text-gray-400 font-medium mb-1">${m.employee_npk}</p>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full border ${badgeClass}">${m.role}</span>
                    </div>
                </div>`;
        });
        openModal('modalDetailCircle');
    }

    function openEditCircle(circle, currentMemberNpks) {
        document.getElementById('formUpdateCircle').action = `/qcc/karyawan/update-circle/${circle.id}`;
        document.getElementById('edit_circle_name').value = circle.circle_name;
        const checkboxes = document.querySelectorAll('.edit-member-checkbox');
        checkboxes.forEach(cb => { cb.checked = currentMemberNpks.includes(cb.value); });
        openModal('modalEditCircle');
    }

    function confirmDeleteCircle(id, name) {
        Swal.fire({
            title: 'Hapus Circle?',
            html: `Apakah Anda yakin ingin menghapus <b>${name}</b>?<br><small class="text-red-500">Semua data tema dan progres PDCA akan ikut terhapus!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Hapus Sekarang!',
            cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) document.getElementById('delete-circle-' + id).submit(); });
    }

    document.getElementById('memberSearchCreate')?.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('.member-card-create').forEach(card => {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(keyword) ? 'flex' : 'none';
        });
    });

    document.getElementById('memberSearchEdit')?.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('.member-card-edit').forEach(card => {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(keyword) ? 'flex' : 'none';
        });
    });

    document.getElementById('formStoreCircle')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Buat Circle Baru?', text: "Data akan disimpan sebagai kelompok QCC resmi.", icon: 'question', showCancelButton: true, confirmButtonColor: '#091E6E', confirmButtonText: 'Ya, Buat!' }).then((result) => { if (result.isConfirmed) this.submit(); });
    });

    document.getElementById('formUpdateCircle')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Simpan Perubahan?', text: "Data anggota akan disinkronkan ulang.", icon: 'question', showCancelButton: true, confirmButtonColor: '#F59E0B', confirmButtonText: 'Ya, Update!' }).then((result) => { if (result.isConfirmed) this.submit(); });
    });

    @if(Session::has('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ Session::get('success') }}", timer: 2000, showConfirmButton: false });
    @elseif(Session::has('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ Session::get('error') }}", confirmButtonColor: '#091E6E' });
    @elseif(Session::has('warning'))
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: "{{ Session::get('warning') }}", confirmButtonColor: '#091E6E' });
    @endif
</script>
@endpush
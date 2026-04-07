@extends('welcome')

@section('title', 'Master User')

@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-4 md:mb-6 text-xs md:text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <i class="fa-solid fa-database mr-2 text-[10px] md:text-xs"></i> Master
            </li>
            <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight text-[10px] md:text-xs">Data User</li>
        </ol>
    </nav>

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Master User</h2>
            <p class="text-xs md:text-sm text-gray-400">Manajemen akun pengguna sistem QCC & SS</p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-start md:justify-end items-center">
            <form action="{{ route('admin.master_user.index') }}" method="GET" id="filterForm" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NPK, atau Email..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm text-xs md:text-sm">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] md:text-xs"></i>
                </div>
            </form>
            <button onclick="openModal('modalAdd')" class="bg-[#091E6E] hover:bg-[#130998] text-white px-4 md:px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg transition-all active:scale-95 text-[10px] md:text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-user-plus"></i> Tambah User
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[900px] md:min-w-full">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-2 md:px-4 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">NPK / Nama</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Email</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold text-center">Role</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold text-center">Status</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $usr)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100">
                        <td class="px-2 md:px-4 py-2 md:py-3 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500 text-xs md:text-sm">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            <p class="font-bold text-[#091E6E] text-xs md:text-sm">{{ $usr->nama }}</p>
                            <p class="text-[8px] md:text-[10px] text-gray-400 font-bold uppercase">NPK: {{ $usr->npk }}</p>
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100 text-xs md:text-sm text-gray-600">
                            {{ $usr->email }}
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100 text-center">
                            <span class="text-[9px] md:text-[11px] font-bold px-2 py-1 rounded-full 
                                {{ $usr->role == 'admin' ? 'bg-red-100 text-red-700' : ($usr->role == 'spv' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ strtoupper($usr->role) }}
                            </span>
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100 text-center">
                            <span class="text-[9px] md:text-[11px] font-bold px-2 py-1 rounded-full {{ $usr->status_user == 'ACTIVE' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $usr->status_user }}
                            </span>
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-1 md:gap-2">
                                <button onclick="openDetailModal({{ json_encode($usr) }})" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-eye text-[8px] md:text-[10px]"></i>
                                </button>
                                <button onclick="openEditModal({{ json_encode($usr) }})" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-pen-to-square text-[8px] md:text-[10px]"></i>
                                </button>
                                <button onclick="confirmDelete('{{ $usr->id }}', '{{ $usr->nama }}')" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-trash text-[8px] md:text-[10px]"></i>
                                </button>
                                <form id="delete-form-{{ $usr->id }}" action="{{ route('admin.master_user.destroy', $usr->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 md:py-20">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <i class="fa-solid fa-folder-open text-3xl md:text-4xl"></i>
                                <span class="italic text-xs md:text-sm">Tidak ada user ditemukan...</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 md:mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-4 md:pt-6">
            <div class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $users->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div id="modalDetail" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-user-circle mr-2"></i> Detail User</h3>
                <button onclick="closeModal('modalDetail')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <div class="p-4 md:p-8 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gray-50 rounded-xl flex items-center justify-center text-xl font-black text-[#091E6E] border border-blue-100" id="det_avatar">U</div>
                    <div>
                        <h4 id="det_nama" class="text-xl font-bold text-[#091E6E]"></h4>
                        <p id="det_npk" class="text-[10px] text-gray-400 font-bold uppercase"></p>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl space-y-2 text-sm">
                    <div><label class="font-bold text-gray-400 text-[10px] uppercase">Email</label><br><span id="det_email"></span></div>
                    <div><label class="font-bold text-gray-400 text-[10px] uppercase">Role</label><br><span id="det_role" class="font-semibold"></span></div>
                    <div><label class="font-bold text-gray-400 text-[10px] uppercase">Status</label><br><span id="det_status"></span></div>
                    <div><label class="font-bold text-gray-400 text-[10px] uppercase">OT PAR</label><br><span id="det_ot_par"></span></div>
                    <div><label class="font-bold text-gray-400 text-[10px] uppercase">Limit MP</label><br><span id="det_limit_mp"></span></div>
                </div>
                <button onclick="closeModal('modalDetail')" class="w-full py-3 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-[10px] hover:bg-gray-200">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL ADD ================= -->
<div id="modalAdd" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-user-plus mr-2"></i> Tambah User</h3>
                <button onclick="closeModal('modalAdd')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <form id="formAdd" action="{{ route('admin.master_user.store') }}" method="POST" class="p-4 md:p-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">NPK</label>
                    <input type="text" name="npk" required placeholder="Contoh: 123456" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-[#091E6E] outline-none text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Nama Lengkap</label>
                    <input type="text" name="nama" required class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-[#091E6E] outline-none text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Email</label>
                    <input type="email" name="email" required class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-[#091E6E] outline-none text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Role</label>
                    <select name="role" required class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm font-semibold">
                        @foreach($roles as $role)
                        <option value="{{ $role }}">{{ strtoupper($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Status User</label>
                    <select name="status_user" required class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none">
                        <option value="ACTIVE">ACTIVE</option>
                        <option value="INACTIVE">INACTIVE</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">OT PAR</label>
                    <input type="text" name="ot_par" placeholder="Opsional" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Limit MP</label>
                    <input type="number" name="limit_mp" placeholder="0" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm">
                </div>
                <div class="sm:col-span-2 text-xs text-gray-400 bg-gray-50 p-2 rounded-lg">
                    <i class="fa-solid fa-info-circle"></i> Password default = NPK user. Segera ubah setelah login.
                </div>
                <button type="submit" class="sm:col-span-2 py-3 bg-[#091E6E] text-white rounded-xl font-bold uppercase tracking-widest text-[10px] hover:bg-[#130998] transition-all active:scale-95">Simpan User</button>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-pen-to-square mr-2"></i> Edit User</h3>
                <button onclick="closeModal('modalEdit')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <form id="formEdit" method="POST" class="p-4 md:p-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf @method('PUT')
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">NPK</label>
                    <input type="text" name="npk" id="edit_npk" required class="w-full mt-1 px-3 py-2 bg-gray-100 border rounded-lg outline-none text-sm font-mono" readonly>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Nama</label>
                    <input type="text" name="nama" id="edit_nama" required class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Email</label>
                    <input type="email" name="email" id="edit_email" required class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Role</label>
                    <select name="role" id="edit_role" required class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none">
                        @foreach($roles as $role)
                        <option value="{{ $role }}">{{ strtoupper($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Status</label>
                    <select name="status_user" id="edit_status_user" required class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none">
                        <option value="ACTIVE">ACTIVE</option>
                        <option value="INACTIVE">INACTIVE</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">OT PAR</label>
                    <input type="text" name="ot_par" id="edit_ot_par" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Limit MP</label>
                    <input type="number" name="limit_mp" id="edit_limit_mp" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Password (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="********" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm">
                </div>
                <div class="flex gap-3 sm:col-span-2 mt-2">
                    <button type="button" onclick="closeModal('modalEdit')" class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-[10px] hover:bg-gray-200">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-amber-500 text-white rounded-xl font-bold uppercase tracking-widest text-[10px] hover:bg-amber-600">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .custom-pagination nav { display: flex; gap: 4px; align-items: center; justify-content: center; }
    .custom-pagination span[aria-current="page"] span { background-color: #091E6E !important; color: white !important; border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 800; }
    .custom-pagination a, .custom-pagination span { border-radius: 8px; padding: 6px 12px; font-size: 11px; border: 1px solid #edf2f7; color: #64748b; transition: all 0.2s; }
    .custom-pagination a:hover { background-color: #f8fafc; border-color: #091E6E; color: #091E6E; }
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

    function openDetailModal(user) {
        document.getElementById('det_nama').innerText = user.nama;
        document.getElementById('det_npk').innerText = 'NPK: ' + user.npk;
        document.getElementById('det_avatar').innerText = user.nama.charAt(0);
        document.getElementById('det_email').innerText = user.email;
        document.getElementById('det_role').innerHTML = `<span class="px-2 py-1 rounded-full text-xs font-bold ${user.role=='admin'?'bg-red-100 text-red-700':'bg-blue-100 text-blue-700'}">${user.role.toUpperCase()}</span>`;
        document.getElementById('det_status').innerHTML = `<span class="px-2 py-1 rounded-full text-xs font-bold ${user.status_user=='ACTIVE'?'bg-green-100 text-green-700':'bg-gray-200 text-gray-600'}">${user.status_user}</span>`;
        document.getElementById('det_ot_par').innerText = user.ot_par || '-';
        document.getElementById('det_limit_mp').innerText = user.limit_mp ?? '-';
        openModal('modalDetail');
    }

    function openEditModal(user) {
        document.getElementById('formEdit').action = `/admin/master-user/${user.id}`;
        document.getElementById('edit_npk').value = user.npk;
        document.getElementById('edit_nama').value = user.nama;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_status_user').value = user.status_user;
        document.getElementById('edit_ot_par').value = user.ot_par || '';
        document.getElementById('edit_limit_mp').value = user.limit_mp || '';
        openModal('modalEdit');
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus User?',
            html: `Apakah Anda yakin ingin menghapus <b>${name}</b>?<br><small class="text-red-500">Tindakan ini tidak dapat dibatalkan.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => { if (result.isConfirmed) document.getElementById('delete-form-' + id).submit(); });
    }

    document.getElementById('formAdd')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Simpan User?', text: 'Password default = NPK', icon: 'question', showCancelButton: true, confirmButtonColor: '#091E6E', confirmButtonText: 'Ya, Simpan!' }).then(res => { if (res.isConfirmed) this.submit(); });
    });

    @if(Session::has('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ Session::get('success') }}", timer: 2000, showConfirmButton: false });
    @endif
    @if(Session::has('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ Session::get('error') }}", timer: 2000 });
    @endif
</script>
@endpush
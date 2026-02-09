@extends('welcome')
@section('title', 'Master Target QCC')
@section('content')
<div class="animate-reveal">
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight">Master Target</li>
        </ol>
    </nav>

    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Master Target QCC</h2>
            <p class="text-sm text-gray-400">Tentukan target jumlah circle per periode & departemen</p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-end items-center">
            <form action="{{ route('qcc.admin.master_targets') }}" method="GET" id="filterForm" class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E]">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Dept atau Periode..." class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm text-sm">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </form>
            <button onclick="openModal('modalAdd')" class="bg-[#091E6E] text-white px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg active:scale-95 text-xs font-bold uppercase tracking-wider transition-all">
                <i class="fa-solid fa-crosshairs"></i> Set Target
            </button>
        </div>
    </div>

    <div class="glass-card rounded-[2rem] p-6 shadow-sm border border-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="sidebar-gradient shadow-md text-white text-[10px] uppercase tracking-widest font-bold">
                        <th class="px-6 py-4 rounded-tl-2xl">Departemen</th>
                        <th class="px-6 py-4">Periode</th>
                        <th class="px-6 py-4 text-center">Target (Circle)</th>
                        <th class="px-6 py-4 text-center rounded-tr-2xl">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($targets as $t)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100">
                        <td class="px-6 py-4 rounded-l-xl">
                            <p class="font-bold text-[#091E6E] text-sm uppercase">{{ $t->department->name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-gray-500">{{ $t->period->period_name }} ({{ $t->period->year }})</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-50 text-[#091E6E] font-black px-4 py-1 rounded-lg border border-blue-100">{{ $t->target_amount }}</span>
                        </td>
                        <td class="px-6 py-4 rounded-r-xl text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick="openEditModal({{ json_encode($t) }})" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm"><i class="fa-solid fa-pen-to-square text-[10px]"></i></button>
                                <button onclick="confirmDelete('{{ $t->id }}')" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"><i class="fa-solid fa-trash text-[10px]"></i></button>
                                <form id="delete-form-{{ $t->id }}" action="{{ route('qcc.admin.delete_target', $t->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-20 text-gray-300 italic">Belum ada target yang ditetapkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t pt-6">
            <div class="text-[10px] font-bold text-gray-400 uppercase">Showing {{ $targets->firstItem() ?? 0 }} to {{ $targets->lastItem() ?? 0 }} of {{ $targets->total() }} entries</div>
            <div class="custom-pagination">{{ $targets->links('pagination::tailwind') }}</div>
        </div>
    </div>
</div>

<!-- ================= MODAL ADD ================= -->
<div id="modalAdd" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">  
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fa-solid fa-bullseye mr-2"></i>
                    Set Target Departemen
                </h3>
                <button onclick="closeModal('modalAdd')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="formAdd" action="{{ route('qcc.admin.store_target') }}" method="POST" class="p-8 space-y-5">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Pilih Periode</label>
                    <select name="qcc_period_id" required class="w-full mt-2 px-4 py-3 bg-gray-50 border rounded-xl font-bold text-[#091E6E] focus:ring-2 focus:ring-[#091E6E] outline-none">
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periods as $p) <option value="{{ $p->id }}">{{ $p->period_name }} ({{ $p->year }})</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Pilih Departemen</label>
                    <select name="department_code" required class="w-full mt-2 px-4 py-3 bg-gray-50 border rounded-xl font-bold text-[#091E6E] focus:ring-2 focus:ring-[#091E6E] outline-none">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departments as $d) <option value="{{ $d->code }}">{{ $d->name }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Jumlah Target Circle</label>
                    <input type="number" name="target_amount" required min="1" placeholder="0" class="w-full mt-2 px-4 py-3 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-bold text-[#091E6E]">
                </div>
                <button type="submit" class="w-full py-4 bg-[#091E6E] text-white rounded-xl font-bold shadow-lg hover:bg-[#130998] transition-all uppercase tracking-widest text-xs">Simpan Target</button>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold text-white"><i class="fa-solid fa-pen-to-square mr-2"></i>
                    Update Target
                </h3>
                <button onclick="closeModal('modalEdit')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="formEdit" method="POST" class="p-8 space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Jumlah Target Circle</label>
                    <input type="number" name="target_amount" id="edit_target_amount" required min="1" class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-bold text-[#091E6E]">
                </div>
                <button type="submit" class="w-full py-4 bg-amber-500 text-white rounded-xl font-bold shadow-lg uppercase tracking-widest text-xs hover:bg-amber-600 transition-all">Update Target</button>
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
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.style.overflow = 'auto'; }

    function openEditModal(target) {
        document.getElementById('formEdit').action = `/qcc/admin/master-targets/${target.id}`;
        document.getElementById('edit_target_amount').value = target.target_amount;
        openModal('modalEdit');
    }

    // --- SWEETALERT: KONFIRMASI SIMPAN ---
    document.getElementById('formAdd')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Simpan Target?',
            text: "Pastikan periode dan departemen yang dipilih sudah benar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#091E6E',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) { this.submit(); }
        });
    });

    // --- SWEETALERT: KONFIRMASI UPDATE ---
    document.getElementById('formEdit')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Update Target?',
            text: "Data target akan diperbarui secara permanen.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#F59E0B',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) { this.submit(); }
        });
    });

    // --- SWEETALERT: KONFIRMASI HAPUS ---
    function confirmDelete(id) {
        Swal.fire({ 
            title: 'Hapus Target?', 
            text: "Data target departemen ini akan hilang selamanya.", 
            icon: 'warning', 
            showCancelButton: true, 
            confirmButtonColor: '#EF4444', 
            confirmButtonText: 'Ya, Hapus!' 
        }).then((result) => { 
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit(); 
        })
    }

    // --- HANDLER PESAN SUKSES/ERROR DARI SESSION ---
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
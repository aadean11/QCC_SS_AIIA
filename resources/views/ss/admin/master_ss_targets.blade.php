@extends('welcome')

@section('title', 'Master Target SS')

@section('content')
<div class="animate-reveal">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Monitoring SS', 'icon' => 'fa-regular fa-lightbulb'],
        'Master Target',
    ]])

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Master Target SS</h2>
            <p class="text-xs md:text-sm text-gray-400">Tentukan target jumlah SS per bulan & departemen</p>
        </div>

        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-start md:justify-end items-center">
            <form action="{{ route('ss.admin.master_targets') }}" method="GET" id="filterForm" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E] w-full sm:w-auto">
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent w-full sm:w-auto">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dept, tahun..."
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm text-xs md:text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] md:text-xs"></i>
                </div>
            </form>
            <button onclick="openModal('modalAdd')" class="bg-[#091E6E] text-white px-4 md:px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg active:scale-95 text-[10px] md:text-xs font-bold uppercase tracking-wider transition-all w-full sm:w-auto justify-center">
                <i class="fa-solid fa-crosshairs"></i> Set Target
            </button>
        </div>
    </div>

    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[800px] md:min-w-full">
                <thead>
                    <tr class="sidebar-gradient shadow-md text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold">
                        <th class="px-2 md:px-4 py-3 md:py-4 rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-6 py-3 md:py-4">Departemen</th>
                        <th class="px-3 md:px-6 py-3 md:py-4">Periode</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center">Target (SS)</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center rounded-tr-2xl">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($targets as $t)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100">
                        <td class="px-2 md:px-4 py-2 md:py-4 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500 text-xs md:text-sm">
                            {{ ($targets->currentPage() - 1) * $targets->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-4 border-y">
                            <p class="font-bold text-[#091E6E] text-xs md:text-sm uppercase">{{ $t->department?->name ?? $t->department_code }}</p>
                            <p class="text-[9px] text-gray-400 font-bold">{{ $t->department_code }}</p>
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-4 border-y">
                            <span class="text-[10px] md:text-xs font-bold text-gray-500">{{ $months[$t->month] ?? $t->month }} {{ $t->year }}</span>
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-4 border-y text-center">
                            <span class="bg-blue-50 text-[#091E6E] font-black px-3 md:px-4 py-0.5 md:py-1 rounded-lg border border-blue-100 text-xs md:text-sm">{{ $t->target_amount }}</span>
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-4 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-1 md:gap-2">
                                <button onclick="openEditModal({{ json_encode($t) }})" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-[8px] md:text-[10px]"></i>
                                </button>
                                <button onclick="confirmDelete('{{ $t->id }}')" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus">
                                    <i class="fa-solid fa-trash text-[8px] md:text-[10px]"></i>
                                </button>
                                <form id="delete-form-{{ $t->id }}" action="{{ route('ss.admin.delete_target', $t->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 md:py-20 text-gray-300 italic text-xs md:text-sm">Belum ada target SS yang ditetapkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 md:mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t pt-4 md:pt-6">
            <div class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">
                Showing {{ $targets->firstItem() ?? 0 }} to {{ $targets->lastItem() ?? 0 }} of {{ $targets->total() }} entries
            </div>
            <div class="custom-pagination">{{ $targets->links('pagination::tailwind') }}</div>
        </div>
    </div>
</div>

<div id="modalAdd" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-bullseye mr-2"></i> Set Target SS</h3>
                <button type="button" onclick="closeModal('modalAdd')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <form id="formAdd" action="{{ route('ss.admin.store_target') }}" method="POST" class="p-4 md:p-8 space-y-4 md:space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tahun <span class="text-red-500">*</span></label>
                        <select name="year" required class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border rounded-lg md:rounded-xl font-bold text-[#091E6E] text-xs md:text-sm focus:ring-2 focus:ring-[#091E6E] outline-none">
                            @foreach($years as $y)
                                <option value="{{ $y }}" @selected(old('year', date('Y')) == $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Bulan <span class="text-red-500">*</span></label>
                        <select name="month" required class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border rounded-lg md:rounded-xl font-bold text-[#091E6E] text-xs md:text-sm focus:ring-2 focus:ring-[#091E6E] outline-none">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" @selected(old('month', date('n')) == $num)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Departemen <span class="text-red-500">*</span></label>
                    <select name="department_code" required class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border rounded-lg md:rounded-xl font-bold text-[#091E6E] text-xs md:text-sm focus:ring-2 focus:ring-[#091E6E] outline-none">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->code }}">{{ $d->name }} ({{ $d->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Target Jumlah SS <span class="text-red-500">*</span></label>
                    <input type="number" name="target_amount" required min="1" placeholder="0" class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border rounded-lg md:rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-bold text-[#091E6E] text-xs md:text-sm">
                </div>
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Keterangan (opsional)</label>
                    <textarea name="description" rows="2" class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border rounded-lg md:rounded-xl text-xs md:text-sm focus:ring-2 focus:ring-[#091E6E] outline-none"></textarea>
                </div>
                <button type="submit" class="w-full py-3 md:py-4 bg-[#091E6E] text-white rounded-xl font-bold shadow-lg hover:bg-[#130998] transition-all uppercase tracking-widest text-[10px] md:text-xs">Simpan Target</button>
            </form>
        </div>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2.5rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold text-white"><i class="fa-solid fa-pen-to-square mr-2"></i> Update Target</h3>
                <button type="button" onclick="closeModal('modalEdit')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <form id="formEdit" method="POST" class="p-4 md:p-8 space-y-4 md:space-y-5">
                @csrf @method('PUT')
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 text-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Periode & Dept</p>
                    <p id="edit_period_label" class="font-bold text-[#091E6E] mt-1"></p>
                </div>
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Target Jumlah SS <span class="text-red-500">*</span></label>
                    <input type="number" name="target_amount" id="edit_target_amount" required min="1" class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-bold text-[#091E6E] text-xs md:text-sm">
                </div>
                <div>
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Keterangan</label>
                    <textarea name="description" id="edit_description" rows="2" class="w-full mt-1 md:mt-2 px-3 md:px-4 py-2 md:py-3 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl text-xs md:text-sm focus:ring-2 focus:ring-amber-500 outline-none"></textarea>
                </div>
                <button type="submit" class="w-full py-3 md:py-4 bg-amber-500 text-white rounded-xl font-bold shadow-lg uppercase tracking-widest text-[10px] md:text-xs hover:bg-amber-600 transition-all">Update Target</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .custom-pagination nav { display: flex; align-items: center; justify-content: center; gap: 4px; }
    .custom-pagination span[aria-current="page"] > span {
        background-color: #091E6E !important; color: white !important; border: none !important;
        border-radius: 8px !important; padding: 6px 12px !important; font-size: 11px !important; font-weight: 800;
    }
</style>
<script>
    const monthNames = @json($months);
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.style.overflow = 'auto'; }

    function openEditModal(target) {
        document.getElementById('formEdit').action = `/ss/admin/master-targets/${target.id}`;
        document.getElementById('edit_target_amount').value = target.target_amount;
        document.getElementById('edit_description').value = target.description || '';
        const monthLabel = monthNames[target.month] || target.month;
        document.getElementById('edit_period_label').textContent = `${monthLabel} ${target.year} · ${target.department_code}`;
        openModal('modalEdit');
    }

    function confirmDelete(id) {
        Swal.fire({ title: 'Hapus Target?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', confirmButtonText: 'Ya, Hapus!' })
            .then((r) => { if (r.isConfirmed) document.getElementById('delete-form-' + id).submit(); });
    }

    document.getElementById('formAdd')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Simpan Target?', icon: 'question', showCancelButton: true, confirmButtonColor: '#091E6E', confirmButtonText: 'Ya, Simpan!' })
            .then((r) => { if (r.isConfirmed) this.submit(); });
    });

    @if(Session::has('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: @json(Session::get('success')), timer: 2500, showConfirmButton: false });
    @endif
    @if(Session::has('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: @json(Session::get('error')), confirmButtonColor: '#091E6E' });
    @endif
</script>
@endpush

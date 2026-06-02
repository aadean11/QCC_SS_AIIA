@extends('welcome')

@section('title', 'Master Scoring SS')

@section('content')
<div class="animate-reveal">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Monitoring SS', 'icon' => 'fa-regular fa-lightbulb'],
        'Master Scoring',
    ]])

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Master Scoring SS</h2>
            <p class="text-xs md:text-sm text-gray-400">Kelola klasifikasi hadiah berdasarkan total nilai SS</p>
        </div>

        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-start md:justify-end items-center">
            <form action="{{ route('ss.admin.master_scoring') }}" method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm hover:border-[#091E6E] w-full sm:w-auto">
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent w-full sm:w-auto">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari penilai/ranking..."
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm text-xs md:text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] md:text-xs"></i>
                </div>
            </form>
            <button onclick="openModal('modalAdd')" class="bg-[#091E6E] hover:bg-[#130998] text-white px-4 md:px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg transition-all active:scale-95 text-[10px] md:text-xs font-bold uppercase tracking-wider w-full sm:w-auto justify-center">
                <i class="fa-solid fa-plus"></i> Tambah Range
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <span class="text-[10px] font-bold text-gray-400 uppercase">Range Aktif</span>
            <p class="text-2xl font-black text-[#091E6E] mt-1">{{ $ranges->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm md:col-span-3">
            <span class="text-[10px] font-bold text-gray-400 uppercase">Aturan Tambahan</span>
            <p class="text-xs md:text-sm text-gray-600 mt-1">Range dengan kolom tambahan akan dipakai untuk nilai di atas batas tertinggi, misalnya di atas 168 setiap penambahan 27 poin ditambah Rp 3.500.</p>
        </div>
    </div>

    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[1100px] md:min-w-full">
                <thead>
                    <tr class="sidebar-gradient shadow-md text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold">
                        <th class="px-3 md:px-4 py-3 md:py-4 rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-5 py-3 md:py-4 text-center">Total Nilai</th>
                        <th class="px-3 md:px-5 py-3 md:py-4 text-center">Ranking</th>
                        <th class="px-3 md:px-5 py-3 md:py-4 text-right">Hadiah</th>
                        <th class="px-3 md:px-5 py-3 md:py-4">Penilai</th>
                        <th class="px-3 md:px-5 py-3 md:py-4">Keterangan</th>
                        <th class="px-3 md:px-5 py-3 md:py-4 text-center">Tambahan</th>
                        <th class="px-3 md:px-5 py-3 md:py-4 text-center">Status</th>
                        <th class="px-3 md:px-5 py-3 md:py-4 rounded-tr-2xl text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ranges as $range)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100">
                        <td class="px-3 md:px-4 py-3 md:py-4 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500 text-xs md:text-sm">
                            {{ ($ranges->currentPage() - 1) * $ranges->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-3 md:px-5 py-3 md:py-4 border-y text-center">
                            <span class="bg-blue-50 text-[#091E6E] font-black px-3 py-1 rounded-lg border border-blue-100 text-xs md:text-sm">{{ $range->min_score }} - {{ $range->max_score }}</span>
                        </td>
                        <td class="px-3 md:px-5 py-3 md:py-4 border-y text-center font-bold text-gray-700">{{ $range->ranking }}</td>
                        <td class="px-3 md:px-5 py-3 md:py-4 border-y text-right font-black text-emerald-600">Rp {{ number_format($range->reward_amount, 0, ',', '.') }}</td>
                        <td class="px-3 md:px-5 py-3 md:py-4 border-y">
                            <span class="text-xs font-bold text-[#091E6E]">{{ $range->approver_level ?? '-' }}</span>
                        </td>
                        <td class="px-3 md:px-5 py-3 md:py-4 border-y">
                            <p class="text-[10px] md:text-xs text-gray-500 line-clamp-2 max-w-md">{{ $range->description ?? '-' }}</p>
                        </td>
                        <td class="px-3 md:px-5 py-3 md:py-4 border-y text-center">
                            @if($range->extra_score_increment && $range->extra_reward_increment)
                                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 border border-amber-100 rounded-lg px-2 py-1">+{{ $range->extra_score_increment }} nilai / Rp {{ number_format($range->extra_reward_increment, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-3 md:px-5 py-3 md:py-4 border-y text-center">
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $range->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">{{ $range->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-3 md:px-5 py-3 md:py-4 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick="openEditModal(@json($range))" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </button>
                                <button onclick="confirmDelete('{{ $range->id }}')" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                                <form id="delete-form-{{ $range->id }}" action="{{ route('ss.admin.delete_scoring', $range->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-16 text-gray-300 italic text-xs md:text-sm">Belum ada master scoring SS.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 md:mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t pt-4 md:pt-6">
            <div class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">
                Showing {{ $ranges->firstItem() ?? 0 }} to {{ $ranges->lastItem() ?? 0 }} of {{ $ranges->total() }} entries
            </div>
            <div class="custom-pagination">{{ $ranges->links('pagination::tailwind') }}</div>
        </div>
    </div>
</div>

@php
    $formFields = [
        ['name' => 'min_score', 'label' => 'Total Nilai Dari', 'type' => 'number', 'min' => 0],
        ['name' => 'max_score', 'label' => 'Total Nilai Sampai', 'type' => 'number', 'min' => 0],
        ['name' => 'ranking', 'label' => 'Ranking', 'type' => 'number', 'min' => 1],
        ['name' => 'reward_amount', 'label' => 'Hadiah (Rp)', 'type' => 'number', 'min' => 0, 'step' => 500],
        ['name' => 'approver_level', 'label' => 'Penilai / Approval', 'type' => 'text'],
    ];
@endphp

<div id="modalAdd" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-2xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-ranking-star mr-2"></i>Tambah Master Scoring</h3>
                <button onclick="closeModal('modalAdd')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <form id="formAdd" action="{{ route('ss.admin.store_scoring') }}" method="POST" class="p-4 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @foreach($formFields as $field)
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">{{ $field['label'] }}</label>
                        <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" @if(isset($field['min'])) min="{{ $field['min'] }}" @endif @if(isset($field['step'])) step="{{ $field['step'] }}" @endif required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-bold text-[#091E6E] text-sm">
                    </div>
                @endforeach
                <div class="md:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Keterangan</label>
                    <textarea name="description" rows="3" class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none text-sm"></textarea>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tambahan Nilai</label>
                    <input type="number" name="extra_score_increment" min="1" class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-bold text-[#091E6E] text-sm" placeholder="Contoh: 27">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tambahan Hadiah (Rp)</label>
                    <input type="number" name="extra_reward_increment" min="0" step="500" class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-bold text-[#091E6E] text-sm" placeholder="Contoh: 3500">
                </div>
                <label class="md:col-span-2 flex items-center gap-2 text-xs font-bold text-[#091E6E]">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
                    Aktif
                </label>
                <button type="submit" class="md:col-span-2 w-full py-4 bg-[#091E6E] text-white rounded-xl font-bold shadow-lg hover:bg-[#130998] transition-all uppercase tracking-widest text-xs">Simpan Master Scoring</button>
            </form>
        </div>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-2xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-pen-to-square mr-2"></i>Edit Master Scoring</h3>
                <button onclick="closeModal('modalEdit')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <form id="formEdit" method="POST" class="p-4 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf @method('PUT')
                @foreach($formFields as $field)
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">{{ $field['label'] }}</label>
                        <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" id="edit_{{ $field['name'] }}" @if(isset($field['min'])) min="{{ $field['min'] }}" @endif @if(isset($field['step'])) step="{{ $field['step'] }}" @endif required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-bold text-[#091E6E] text-sm">
                    </div>
                @endforeach
                <div class="md:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Keterangan</label>
                    <textarea name="description" id="edit_description" rows="3" class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-sm"></textarea>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tambahan Nilai</label>
                    <input type="number" name="extra_score_increment" id="edit_extra_score_increment" min="1" class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-bold text-[#091E6E] text-sm">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tambahan Hadiah (Rp)</label>
                    <input type="number" name="extra_reward_increment" id="edit_extra_reward_increment" min="0" step="500" class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-bold text-[#091E6E] text-sm">
                </div>
                <label class="md:col-span-2 flex items-center gap-2 text-xs font-bold text-[#091E6E]">
                    <input type="checkbox" name="is_active" value="1" id="edit_is_active" class="rounded border-gray-300">
                    Aktif
                </label>
                <div class="md:col-span-2 flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="closeModal('modalEdit')" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-xs">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-amber-500 text-white rounded-xl font-bold shadow-lg hover:bg-amber-600 transition-all uppercase tracking-widest text-xs">Update Master</button>
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
        box-shadow: 0 4px 6px -1px rgba(9, 30, 110, 0.2);
    }
    .custom-pagination a, .custom-pagination span {
        border-radius: 8px !important; padding: 6px 12px !important; font-size: 11px !important;
        font-weight: 700 !important; border: 1px solid #edf2f7 !important; color: #64748b;
    }
    .custom-pagination a:hover { background-color: #f8fafc !important; border-color: #091E6E !important; color: #091E6E !important; }
</style>
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.style.overflow = 'auto'; }

    function openEditModal(range) {
        document.getElementById('formEdit').action = `/ss/admin/master-scoring/${range.id}`;
        ['min_score', 'max_score', 'ranking', 'reward_amount', 'approver_level', 'description', 'extra_score_increment', 'extra_reward_increment'].forEach(field => {
            const input = document.getElementById('edit_' + field);
            if (input) input.value = range[field] ?? '';
        });
        document.getElementById('edit_is_active').checked = !!range.is_active;
        openModal('modalEdit');
    }

    document.getElementById('formAdd')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Simpan Master Scoring?', text: 'Range nilai ini akan dipakai untuk kalkulasi reward SS.', icon: 'question', showCancelButton: true, confirmButtonColor: '#091E6E', confirmButtonText: 'Ya, Simpan!' }).then((result) => { if (result.isConfirmed) this.submit(); });
    });

    document.getElementById('formEdit')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Update Master Scoring?', text: 'Perubahan akan mempengaruhi kalkulasi reward berikutnya.', icon: 'question', showCancelButton: true, confirmButtonColor: '#F59E0B', confirmButtonText: 'Ya, Update!' }).then((result) => { if (result.isConfirmed) this.submit(); });
    });

    function confirmDelete(id) {
        Swal.fire({ title: 'Hapus Range Scoring?', text: 'Range yang dihapus tidak akan dipakai lagi untuk kalkulasi reward.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', confirmButtonText: 'Ya, Hapus!' }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        });
    }
</script>
@endpush

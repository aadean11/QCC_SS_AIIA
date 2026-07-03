@extends('welcome')

@section('title', 'Master Sub Section')

@section('content')
<div class="animate-reveal">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Master', 'icon' => 'fa-solid fa-database'],
        'Master Sub Section',
    ]])

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Master Sub Section</h2>
            <p class="text-xs md:text-sm text-gray-400">Manajemen data sub section per section</p>
        </div>

        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-start md:justify-end items-center">
            <form action="{{ route('admin.master_sub_sections.index') }}" method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm">
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, section..."
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm text-xs md:text-sm">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] md:text-xs"></i>
                </div>
            </form>
            <button onclick="openModal('modalAdd')" class="bg-[#091E6E] hover:bg-[#130998] text-white px-4 md:px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg transition-all active:scale-95 text-[10px] md:text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-sitemap"></i> Tambah Sub Section
            </button>
        </div>
    </div>

    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[900px] md:min-w-full">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-2 md:px-4 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Kode / Nama</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Alias</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold text-center">Section</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold text-center">Departemen</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold text-center">NPK</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subSections as $sub)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100">
                        <td class="px-2 md:px-4 py-2 md:py-3 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500 text-xs md:text-sm">
                            {{ ($subSections->currentPage() - 1) * $subSections->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            <p class="font-bold text-[#091E6E] text-xs md:text-sm">{{ $sub->name }}</p>
                            <p class="text-[8px] md:text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Kode: {{ $sub->code }}</p>
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100 text-xs md:text-sm text-gray-600">{{ $sub->alias ?: '-' }}</td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100 text-center">
                            <span class="text-[8px] md:text-[10px] bg-indigo-50 text-indigo-700 px-2 md:px-3 py-0.5 md:py-1 rounded-lg font-bold border border-indigo-100">{{ $sub->code_section }}</span>
                            @if($sub->section)
                                <p class="text-[8px] text-gray-400 mt-1">{{ $sub->section->name }}</p>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100 text-center">
                            @if($sub->section && $sub->section->department)
                                <span class="text-[8px] md:text-[10px] bg-blue-50 text-blue-700 px-2 md:px-3 py-0.5 md:py-1 rounded-lg font-bold border border-blue-100">{{ $sub->section->department->code }}</span>
                                <p class="text-[8px] text-gray-400 mt-1">{{ $sub->section->department->name }}</p>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100 text-center text-xs md:text-sm text-gray-600">{{ $sub->npk ?: '-' }}</td>
                        <td class="px-3 md:px-6 py-2 md:py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-1 md:gap-2">
                                <button onclick="openDetailModal({{ json_encode($sub) }})" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm"><i class="fa-solid fa-eye text-[8px] md:text-[10px]"></i></button>
                                <button onclick="openEditModal({{ json_encode($sub) }})" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm"><i class="fa-solid fa-pen-to-square text-[8px] md:text-[10px]"></i></button>
                                <button onclick="confirmDelete('{{ $sub->id }}', '{{ $sub->code }}', '{{ addslashes($sub->name) }}')" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"><i class="fa-solid fa-trash text-[8px] md:text-[10px]"></i></button>
                                <form id="delete-form-{{ $sub->id }}" action="{{ route('admin.master_sub_sections.destroy', $sub->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 md:py-20">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <i class="fa-solid fa-folder-open text-3xl md:text-4xl"></i>
                                <span class="italic text-xs md:text-sm">Data sub section tidak ditemukan...</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 md:mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-4 md:pt-6">
            <div class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $subSections->firstItem() ?? 0 }} to {{ $subSections->lastItem() ?? 0 }} of {{ $subSections->total() }} entries
            </div>
            <div class="custom-pagination">{{ $subSections->links('pagination::tailwind') }}</div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div id="modalDetail" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-sitemap mr-2"></i> Detail Sub Section</h3>
                <button onclick="closeModal('modalDetail')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <div class="p-4 md:p-8 space-y-4">
                <div><h4 id="det_name" class="text-xl font-bold text-[#091E6E]"></h4><p id="det_code" class="text-sm text-gray-400 font-bold uppercase"></p></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div><label class="text-[10px] font-bold text-gray-400 uppercase">Alias</label><p id="det_alias" class="text-[#091E6E] font-bold text-sm"></p></div>
                    <div><label class="text-[10px] font-bold text-gray-400 uppercase">Section</label><p id="det_section" class="text-[#091E6E] font-bold text-sm"></p></div>
                    <div class="col-span-1 sm:col-span-2"><label class="text-[10px] font-bold text-gray-400 uppercase">Departemen</label><p id="det_department" class="text-[#091E6E] font-bold text-sm"></p></div>
                    <div class="col-span-1 sm:col-span-2"><label class="text-[10px] font-bold text-gray-400 uppercase">NPK</label><p id="det_npk" class="text-[#091E6E] font-bold text-sm"></p></div>
                </div>
                <button onclick="closeModal('modalDetail')" class="w-full py-3 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase text-xs">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div id="modalAdd" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-sitemap mr-2"></i> Tambah Sub Section</h3>
                <button onclick="closeModal('modalAdd')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <form id="formAdd" action="{{ route('admin.master_sub_sections.store') }}" method="POST" class="p-4 md:p-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Kode <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required maxlength="50" pattern="[A-Za-z0-9._-]+" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-[#091E6E] outline-none text-sm font-bold uppercase">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">NPK <span class="text-red-500">*</span></label>
                    <input type="text" name="npk" value="{{ old('npk') }}" required maxlength="6" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-[#091E6E] outline-none text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Nama Sub Section <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-[#091E6E] outline-none text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Alias <span class="text-red-500">*</span></label>
                    <input type="text" name="alias" value="{{ old('alias') }}" required maxlength="255" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-[#091E6E] outline-none text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Section <span class="text-red-500">*</span></label>
                    <select name="code_section" required class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm font-semibold">
                        <option value="">-- Pilih Section --</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->code }}" @selected(old('code_section') === $sec->code)>{{ $sec->code }} - {{ $sec->name }} ({{ $sec->code_department }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="sm:col-span-2 py-3 bg-[#091E6E] text-white rounded-xl font-bold shadow-lg uppercase text-xs">Simpan Sub Section</button>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4 text-left">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-4 md:p-6 text-white flex justify-between items-center">
                <h3 class="text-base md:text-xl font-bold"><i class="fa-solid fa-pen-to-square mr-2"></i> Update Sub Section</h3>
                <button onclick="closeModal('modalEdit')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <form id="formEdit" method="POST" class="p-4 md:p-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf @method('PUT')
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Kode <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="edit_code" required maxlength="50" pattern="[A-Za-z0-9._-]+" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none text-sm font-bold uppercase">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">NPK <span class="text-red-500">*</span></label>
                    <input type="text" name="npk" id="edit_npk" required maxlength="6" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Nama Sub Section <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit_name" required maxlength="255" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Alias <span class="text-red-500">*</span></label>
                    <input type="text" name="alias" id="edit_alias" required maxlength="255" class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Section <span class="text-red-500">*</span></label>
                    <select name="code_section" id="edit_code_section" required class="w-full mt-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm font-semibold">
                        @foreach($sections as $sec)
                            <option value="{{ $sec->code }}">{{ $sec->code }} - {{ $sec->name }} ({{ $sec->code_department }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 sm:col-span-2">
                    <button type="button" onclick="closeModal('modalEdit')" class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase text-xs">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-amber-500 text-white rounded-xl font-bold shadow-lg uppercase text-xs">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('admin.partials.master_org_scripts')
<script>
    const masterUrl = @json(url('/admin/master-sub-sections'));

    function openDetailModal(item) {
        document.getElementById('det_name').innerText = item.name;
        document.getElementById('det_code').innerText = 'Kode: ' + item.code;
        document.getElementById('det_alias').innerText = item.alias || '-';
        document.getElementById('det_section').innerText = item.code_section + (item.section ? ' - ' + item.section.name : '');
        const dept = item.section?.department;
        document.getElementById('det_department').innerText = dept ? `${dept.code} - ${dept.name}` : '-';
        document.getElementById('det_npk').innerText = item.npk || '-';
        openModal('modalDetail');
    }

    function openEditModal(item) {
        document.getElementById('formEdit').action = `${masterUrl}/${item.id}`;
        document.getElementById('edit_code').value = item.code;
        document.getElementById('edit_name').value = item.name;
        document.getElementById('edit_alias').value = item.alias || '';
        document.getElementById('edit_code_section').value = item.code_section;
        document.getElementById('edit_npk').value = item.npk || '';
        openModal('modalEdit');
    }

    function confirmDelete(id, code, name) {
        confirmDeleteMaster(id, 'Hapus Sub Section?', `Apakah Anda yakin ingin menghapus sub section <b>${code}</b> - ${name}?<br><small class="text-red-500 italic">Sub section yang masih digunakan pada data karyawan tidak dapat dihapus.</small>`);
    }

    bindMasterFormConfirm('formAdd', 'Simpan Sub Section?', 'Pastikan semua field terisi dengan benar.');
    bindMasterFormConfirm('formEdit', 'Update Sub Section?', 'Perubahan data sub section akan disimpan secara permanen.', '#F59E0B', 'Ya, Update!');

    document.addEventListener('DOMContentLoaded', function() {
        const successMsg = @json(session('success'));
        if (successMsg) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: successMsg,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        const errorMsg = @json(session('error'));
        if (errorMsg) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: errorMsg,
                confirmButtonColor: '#d33'
            });
        }

        @if($errors->any())
            let errorHtml = '<ul style="text-align:left;">';
            @foreach($errors->all() as $error)
                errorHtml += '<li>{{ $error }}</li>';
            @endforeach
            errorHtml += '</ul>';
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: errorHtml,
                confirmButtonColor: '#d33'
            });
        @endif
    });
</script>
@endpush
@extends('welcome')

@section('title', 'Master Periods QCC')

@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight">Master Periods</li>
        </ol>
    </nav>

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Master Periods QCC</h2>
            <p class="text-sm text-gray-400">Kelola durasi periode dan deadline langkah QCC</p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-end items-center">
            <form action="{{ route('qcc.admin.master_periods') }}" method="GET" id="filterForm" class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E]">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari periode/tahun..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm transition-all text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </form>
            <button onclick="openModal('modalAdd')" class="bg-[#091E6E] hover:bg-[#130998] text-white px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg transition-all active:scale-95 text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-calendar-plus"></i> Tambah Periode
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[2rem] p-6 shadow-sm ">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl text-center">Tahun</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold">Periode & Durasi</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-[0.2em] font-bold text-center">Status</th>
                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $period)
                    <tr class="bg-white hover:shadow-md transition-all group shadow-sm border border-gray-100">
                        <td class="px-6 py-3 rounded-l-xl border-y border-l border-gray-100 text-center font-black text-[#091E6E]">
                            {{ $period->year }}
                        </td>
                        <td class="px-6 py-3 border-y border-gray-100">
                            <div class="flex flex-col">
                                <span class="font-bold text-[#091E6E] text-sm">{{ $period->period_name }} ({{ $period->period_code }})</span>
                                <span class="text-[11px] text-gray-400 font-medium">
                                    <i class="fa-regular fa-calendar mr-1"></i> {{ date('d M Y', strtotime($period->start_date)) }} - {{ date('d M Y', strtotime($period->end_date)) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-3 border-y border-gray-100 text-center">
                            @php
                                $statusClasses = [
                                    'ACTIVE' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'INACTIVE' => 'bg-gray-50 text-gray-400 border-gray-100',
                                    'CLOSED' => 'bg-red-50 text-red-600 border-red-100',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border {{ $statusClasses[$period->status] ?? $statusClasses['INACTIVE'] }}">
                                {{ $period->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick="openDetailModal({{ json_encode($period) }}, {{ json_encode($period->periodSteps) }})" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="View Detail">
                                    <i class="fa-solid fa-eye text-[10px]"></i>
                                </button>
                                <button onclick="openDeadlineModal({{ json_encode($period) }}, {{ json_encode($period->periodSteps) }})" class="w-8 h-8 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Set Deadlines">
                                    <i class="fa-solid fa-list-check text-[10px]"></i>
                                </button>
                                <button onclick="openEditModal({{ $period }})" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                </button>
                                <button onclick="confirmDelete('{{ $period->id }}')" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Hapus">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                                <form id="delete-form-{{ $period->id }}" action="{{ route('qcc.admin.delete_period', $period->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-10 text-gray-300 italic text-sm">Belum ada periode.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-6">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $periods->firstItem() ?? 0 }} to {{ $periods->lastItem() ?? 0 }} of {{ $periods->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $periods->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div id="modalDetail" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-2xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fa-solid fa-circle-info mr-2"></i>
                    Detail Periode
                </h3>
                <button onclick="closeModal('modalDetail')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-6 rounded-2xl border border-gray-100 shadow-inner font-medium">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama & Kode</label>
                        <p id="det_name" class="text-[#091E6E] font-bold"></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tahun</label>
                        <p id="det_year" class="text-[#091E6E] font-bold"></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Durasi Periode</label>
                        <p id="det_range" class="text-[#091E6E] text-sm font-bold"></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Status</label>
                        <span id="det_status" class="px-3 py-0.5 rounded-full text-[10px] font-bold border"></span>
                    </div>
                </div>
                <div class="border-t pt-4">
                    <h4 class="text-xs font-bold text-[#091E6E] uppercase tracking-widest mb-4">Target Deadline Per Langkah</h4>
                    <div id="det_steps" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Content via JS -->
                    </div>
                </div>
                <button onclick="closeModal('modalDetail')" class="w-full py-4 bg-gray-300 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-gray-200 transition-all">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL ADD PERIOD ================= -->
<div id="modalAdd" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-xl shadow-2xl animate-reveal overflow-hidden">  
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fa-solid fa-calendar-plus mr-2"></i>
                    Tambah Periode Baru
                </h3>
                <button onclick="closeModal('modalAdd')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="formAdd" action="{{ route('qcc.admin.store_period') }}" method="POST" class="p-8 grid grid-cols-2 gap-6">
                @csrf
                <div class="col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Periode</label>
                    <input type="text" name="period_name" required placeholder="Contoh: Periode Genap" class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none transition-all font-medium text-[#091E6E]">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Kode Periode</label>
                    <input type="text" name="period_code" required placeholder="Contoh: P2-2025" class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-medium text-[#091E6E]">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tahun</label>
                    <input type="number" name="year" value="{{ date('Y') }}" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-medium text-[#091E6E]">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="add_start_date" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-medium text-[#091E6E]">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="add_end_date" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] outline-none font-medium text-[#091E6E]">
                </div>
                <div class="col-span-2 pt-4">
                    <p class="text-[10px] text-gray-400 italic">* Status periode awal akan otomatis diset <span class="font-bold text-emerald-500">ACTIVE</span>.</p>
                </div>
                <button type="submit" class="col-span-2 py-4 bg-[#091E6E] text-white rounded-xl font-bold shadow-lg hover:bg-[#130998] transition-all uppercase tracking-widest text-xs">Simpan Data Periode</button>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT PERIOD ================= -->
<div id="modalEdit" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2.5rem] w-full max-w-xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fa-solid fa-pen-to-square mr-2"></i>
                    Update Data Periode
                </h3>
                <button onclick="closeModal('modalEdit')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="formEdit" method="POST" class="p-8 grid grid-cols-2 gap-6">
                @csrf @method('PUT')
                <div class="col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Periode</label>
                    <input type="text" name="period_name" id="edit_period_name" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-medium text-[#091E6E]">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Kode Periode</label>
                    <input type="text" name="period_code" id="edit_period_code" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-medium text-[#091E6E]">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tahun</label>
                    <input type="number" name="year" id="edit_year" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-medium text-[#091E6E]">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="edit_start_date" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-medium text-[#091E6E]">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="edit_end_date" required class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-medium text-[#091E6E]">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Status Periode</label>
                    <select name="status" id="edit_status" class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none font-medium text-[#091E6E]">
                        <option value="ACTIVE">ACTIVE</option>
                        <option value="INACTIVE">INACTIVE</option>
                        <option value="CLOSED">CLOSED</option>
                    </select>
                </div>
                <div class="flex gap-3 col-span-2 mt-4">
                    <button type="button" onclick="closeModal('modalEdit')" class="flex-1 py-4 bg-gray-300 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-[10px]">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-amber-500 text-white rounded-xl font-bold shadow-lg uppercase tracking-widest text-[10px] hover:bg-amber-600 transition-all">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL DEADLINE ================= -->
<div id="modalDeadline" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2rem] w-full max-w-2xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold"><i class="fa-solid fa-sliders mr-2"></i>
                    Set Deadlines Langkah
                </h3>
                <button onclick="closeModal('modalDeadline')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="formDeadline" method="POST" class="p-8">
                @csrf @method('PUT')
                <div class="mb-4">
                    <h4 id="deadlinePeriodName" class="text-[#091E6E] font-bold text-lg"></h4>
                </div>
                <div class="overflow-x-auto border rounded-2xl overflow-hidden mb-6">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase">Step QCC</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase text-center">Batas Akhir (Deadline)</th>
                            </tr>
                        </thead>
                        <tbody id="deadlineList"></tbody>
                    </table>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('modalDeadline')" class="flex-1 py-4 bg-gray-300 text-gray-500 rounded-xl font-bold uppercase tracking-widest text-xs">Tutup</button>
                    <button type="submit" class="flex-1 py-4 bg-[#091E6E] text-white rounded-xl font-bold shadow-lg uppercase tracking-widest text-xs hover:bg-[#130998]">Update Deadline</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.style.overflow = 'auto'; }

    // --- DETAIL LOGIC ---
    function openDetailModal(period, periodSteps) {
        document.getElementById('det_name').innerText = period.period_name + ' (' + period.period_code + ')';
        document.getElementById('det_year').innerText = period.year;
        document.getElementById('det_range').innerText = period.start_date + ' s/d ' + period.end_date;
        
        const badge = document.getElementById('det_status');
        badge.innerText = period.status;
        const s = period.status;
        badge.className = `px-3 py-1 rounded-full text-[10px] font-bold uppercase border ${s == 'ACTIVE' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : (s == 'CLOSED' ? 'bg-red-50 text-red-600 border-red-100' : 'bg-gray-50 text-gray-400 border-gray-100')}`;

        const stepsCont = document.getElementById('det_steps');
        stepsCont.innerHTML = '';
        periodSteps.forEach(ps => {
            stepsCont.innerHTML += `
                <div class="flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100">
                    <span class="font-bold text-[#091E6E]">Step ${ps.step.step_number}</span>
                    <span class="text-gray-400 font-bold tracking-tighter">${ps.deadline_date}</span>
                </div>
            `;
        });
        openModal('modalDetail');
    }

    // --- DEADLINE LOGIC ---
    function openDeadlineModal(period, periodSteps) {
        document.getElementById('formDeadline').action = `/qcc/admin/master-periods/${period.id}`;
        document.getElementById('deadlinePeriodName').innerText = period.period_name + ' (' + period.year + ')';
        
        const list = document.getElementById('deadlineList');
        list.innerHTML = '';
        
        periodSteps.forEach(ps => {
            list.innerHTML += `
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="text-xs font-bold text-[#091E6E]">Step ${ps.step.step_number}</span><br>
                        <span class="text-[9px] text-gray-400 font-black">${ps.step.step_name}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <input type="date" name="deadlines[${ps.qcc_step_id}]" value="${ps.deadline_date}" 
                            min="${period.start_date}" max="${period.end_date}"
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs outline-none focus:ring-1 focus:ring-[#091E6E] font-bold text-[#091E6E]">
                    </td>
                </tr>
            `;
        });
        openModal('modalDeadline');
    }

    // --- EDIT LOGIC ---
    function openEditModal(period) {
        document.getElementById('formEdit').action = `/qcc/admin/master-periods/${period.id}`;
        document.getElementById('edit_period_name').value = period.period_name;
        document.getElementById('edit_period_code').value = period.period_code;
        document.getElementById('edit_year').value = period.year;
        document.getElementById('edit_start_date').value = period.start_date;
        document.getElementById('edit_end_date').value = period.end_date;
        document.getElementById('edit_status').value = period.status;
        openModal('modalEdit');
    }

    // --- SUCCESS LOGIC ---
    @if(Session::has('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ Session::get('success') }}", showConfirmButton: false, timer: 2500, background: '#ffffff', iconColor: '#10B981', customClass: { title: 'text-[#091E6E] font-bold' } });
    @endif

    // --- AUTO-OPEN DEADLINE AFTER CREATE ---
    @if(Session::has('auto_open_deadline'))
        const newPeriodData = @json(Session::get('auto_open_deadline'));
        openDeadlineModal(newPeriodData, newPeriodData.period_steps);
    @endif

    // --- CONFIRMATIONS ---
    document.getElementById('formAdd').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Simpan Periode?', text: "Status awal otomatis ACTIVE dan 8 langkah akan digenerate.", icon: 'question', showCancelButton: true, confirmButtonColor: '#091E6E', confirmButtonText: 'Ya, Simpan!' }).then((result) => { if (result.isConfirmed) this.submit(); });
    });

    document.getElementById('formEdit').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Update Periode?', text: "Data periode akan diperbarui di database.", icon: 'question', showCancelButton: true, confirmButtonColor: '#F59E0B', confirmButtonText: 'Ya, Update!' }).then((result) => { if (result.isConfirmed) this.submit(); });
    });

    document.getElementById('formDeadline').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Simpan Deadline?', text: "Batas waktu pengumpulan per langkah akan diperbarui.", icon: 'question', showCancelButton: true, confirmButtonColor: '#091E6E', confirmButtonText: 'Ya, Simpan!' }).then((result) => { if (result.isConfirmed) this.submit(); });
    });

    function confirmDelete(id) {
        Swal.fire({ title: 'Hapus Periode?', text: "Data deadline dan data circle terkait akan ikut terhapus!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', confirmButtonText: 'Ya, Hapus!' }).then((result) => { if (result.isConfirmed) document.getElementById('delete-form-' + id).submit(); })
    }
</script>
@endpush
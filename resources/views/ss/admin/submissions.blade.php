@extends('welcome')

@section('title', 'Daftar Ide SS')

@section('content')
@php
    $dateFrom = request('date_from');
    $dateTo   = request('date_to');

    $periodDisplay = ($dateFrom && $dateTo)
        ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') . ' — ' . \Carbon\Carbon::parse($dateTo)->format('d/m/Y')
        : ($dateFrom ? 'Dari ' . \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : ($dateTo ? 'Sampai ' . \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : ''));

    $hasDateFilter = filled($dateFrom) || filled($dateTo);

    // Label kolom tanggal sesuai status yang dipilih
    $selectedStatus = request('status');
    $dateColumnLabel = match($selectedStatus) {
        'approved'     => 'Tanggal Approved',
        'rewarded'     => 'Tanggal Reward Dibayar',
        'admin_review' => 'Tanggal Admin Review',
        'kdp_review'   => 'Tanggal KDP Review',
        'spv_review'   => 'Tanggal SPV Review',
        default        => 'Tanggal Pengajuan',
    };
@endphp

<div class="animate-reveal">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Monitoring SS', 'icon' => 'fa-regular fa-lightbulb'],
        'Daftar Ide',
    ]])

    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Daftar Ide SS</h2>
        <p class="text-xs md:text-sm text-gray-400">Kelola semua pengajuan Suggestion System</p>
    </div>

    <!-- Filter Panel Compact -->
    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-5 shadow-sm border border-white mb-6 md:mb-8">
        <form method="GET" id="filterForm" class="space-y-4">
            <input type="hidden" name="date_from" id="filterDateFrom" value="{{ $dateFrom }}">
            <input type="hidden" name="date_to"   id="filterDateTo"   value="{{ $dateTo }}">

            <!-- Top row: search + actions -->
            <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama pengaju, departemen..."
                        class="w-full h-11 pl-10 pr-4 bg-white border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm transition-all text-xs md:text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] md:text-xs"></i>
                </div>

                <div class="flex items-center gap-2 lg:ml-auto">
                    @if(request()->hasAny(['search', 'status', 'department_code', 'date_from', 'date_to']) || (request('per_page') && request('per_page') != 10))
                        <a href="{{ route('ss.admin.submissions') }}"
                           class="inline-flex items-center justify-center gap-2 px-4 h-11 rounded-2xl border border-gray-200 bg-white text-gray-500 hover:text-[#091E6E] hover:border-[#091E6E] text-[10px] md:text-xs font-bold uppercase tracking-wider transition-all">
                            <i class="fa-solid fa-rotate-left"></i>
                            Reset
                        </a>
                    @else
                        <span class="hidden lg:inline-flex items-center h-11 px-4 rounded-2xl border border-dashed border-gray-200 text-[10px] md:text-xs font-bold uppercase tracking-wider text-gray-400">
                            Filter aktif otomatis saat dipilih
                        </span>
                    @endif

                    <a href="{{ route('ss.admin.submissions.export_pdf', request()->query()) }}"
                       class="inline-flex items-center justify-center gap-2 px-4 h-11 rounded-2xl bg-red-600 hover:bg-red-700 text-white text-[10px] md:text-xs font-bold uppercase tracking-wider shadow-sm transition-all">
                        <i class="fa-regular fa-file-pdf"></i>
                        Export PDF
                    </a>
                </div>
            </div>

            <!-- Core filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider pl-1">Tampilkan</label>
                    <div class="flex items-center gap-2 bg-white px-3 h-11 rounded-2xl border border-gray-200 shadow-sm">
                        <select name="per_page" onchange="this.form.submit()"
                            class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent w-full">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 data</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 data</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 data</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider pl-1">Status</label>
                    <div class="flex items-center gap-2 bg-white px-3 h-11 rounded-2xl border border-gray-200 shadow-sm">
                        <i class="fa-solid fa-filter text-[10px] text-gray-400 shrink-0"></i>
                        <select name="status" id="statusSelect" onchange="this.form.submit()"
                            class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent w-full min-w-0">
                            <option value="">Semua Status</option>
                            <option value="submitted"    {{ request('status') == 'submitted'    ? 'selected' : '' }}>Submitted</option>
                            <option value="spv_review"   {{ request('status') == 'spv_review'   ? 'selected' : '' }}>Need SPV</option>
                            <option value="kdp_review"   {{ request('status') == 'kdp_review'   ? 'selected' : '' }}>KDP Review</option>
                            <option value="admin_review" {{ request('status') == 'admin_review' ? 'selected' : '' }}>Admin Review</option>
                            <option value="approved"     {{ request('status') == 'approved'     ? 'selected' : '' }}>Approved</option>
                            <option value="rejected"     {{ request('status') == 'rejected'     ? 'selected' : '' }}>Rejected</option>
                            <option value="rewarded"     {{ request('status') == 'rewarded'     ? 'selected' : '' }}>Rewarded</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider pl-1">Departemen</label>
                    <div class="flex items-center gap-2 bg-white px-3 h-11 rounded-2xl border border-gray-200 shadow-sm">
                        <i class="fa-solid fa-building text-[10px] text-gray-400 shrink-0"></i>
                        <select name="department_code" onchange="this.form.submit()"
                            class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent w-full min-w-0">
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->code }}" {{ request('department_code') == $dept->code ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Date filter — Date Range Picker -->
            <div class="flex flex-col gap-2 pt-1">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider pl-1">
                            Periode
                        </label>
                        <!-- Dynamic label showing which date column is being filtered -->
                        <span id="dateColumnBadge"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] md:text-[9px] font-bold uppercase tracking-wider
                                   {{ $selectedStatus ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                            <i class="fa-solid fa-calendar-day text-[7px]"></i>
                            {{ $dateColumnLabel }}
                        </span>
                    </div>
                    @if($hasDateFilter)
                        <button type="button" id="clearPeriod"
                            class="flex items-center gap-1 text-[9px] md:text-[10px] font-bold text-red-400 hover:text-red-600 transition-colors"
                            title="Hapus periode">
                            <i class="fa-solid fa-xmark text-[9px]"></i>
                            Hapus Filter
                        </button>
                    @endif
                </div>

                <!-- Preset buttons -->
                <div class="flex flex-wrap gap-2">
                    <button type="button" data-preset="today" class="period-preset px-3 py-1.5 rounded-full text-[9px] md:text-[10px] font-bold uppercase tracking-wider border transition-all">
                        Hari Ini
                    </button>
                    <button type="button" data-preset="this_week" class="period-preset px-3 py-1.5 rounded-full text-[9px] md:text-[10px] font-bold uppercase tracking-wider border transition-all">
                        Minggu Ini
                    </button>
                    <button type="button" data-preset="this_month" class="period-preset px-3 py-1.5 rounded-full text-[9px] md:text-[10px] font-bold uppercase tracking-wider border transition-all">
                        Bulan Ini
                    </button>
                    <button type="button" data-preset="last_month" class="period-preset px-3 py-1.5 rounded-full text-[9px] md:text-[10px] font-bold uppercase tracking-wider border transition-all">
                        Bulan Lalu
                    </button>
                    <button type="button" data-preset="this_year" class="period-preset px-3 py-1.5 rounded-full text-[9px] md:text-[10px] font-bold uppercase tracking-wider border transition-all">
                        Tahun Ini
                    </button>
                    <button type="button" data-preset="all" class="period-preset px-3 py-1.5 rounded-full text-[9px] md:text-[10px] font-bold uppercase tracking-wider border transition-all">
                        Semua
                    </button>
                </div>

                <!-- Date range pickers -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- From -->
                    <div class="flex flex-col gap-1">
                        <span class="text-[8px] md:text-[9px] font-bold text-gray-400 uppercase tracking-wider pl-1">Dari Tanggal</span>
                        <div class="flex items-center gap-2 bg-white px-3 h-11 rounded-2xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E] focus-within:border-[#091E6E] focus-within:ring-2 focus-within:ring-[#091E6E]/20">
                            <i class="fa-regular fa-calendar text-[10px] text-blue-400 shrink-0"></i>
                            <input
                                type="date"
                                id="dateFromPicker"
                                value="{{ $dateFrom }}"
                                class="flex-1 min-w-0 text-[10px] md:text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer"
                                title="Dari tanggal"
                            >
                        </div>
                    </div>

                    <!-- To -->
                    <div class="flex flex-col gap-1">
                        <span class="text-[8px] md:text-[9px] font-bold text-gray-400 uppercase tracking-wider pl-1">Sampai Tanggal</span>
                        <div class="flex items-center gap-2 bg-white px-3 h-11 rounded-2xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E] focus-within:border-[#091E6E] focus-within:ring-2 focus-within:ring-[#091E6E]/20">
                            <i class="fa-regular fa-calendar text-[10px] text-blue-400 shrink-0"></i>
                            <input
                                type="date"
                                id="dateToPicker"
                                value="{{ $dateTo }}"
                                class="flex-1 min-w-0 text-[10px] md:text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer"
                                title="Sampai tanggal"
                            >
                        </div>
                    </div>
                </div>

                <!-- Active period display -->
                @if($hasDateFilter)
                <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 rounded-xl border border-blue-100">
                    <i class="fa-solid fa-calendar-check text-[10px] text-blue-500"></i>
                    <span class="text-[10px] md:text-xs font-bold text-[#091E6E]">{{ $periodDisplay }}</span>
                    @if($selectedStatus)
                        <span class="text-[9px] text-blue-400 font-medium">· filter berdasarkan {{ strtolower($dateColumnLabel) }}</span>
                    @endif
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-5 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[900px] md:min-w-full">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-2 md:px-4 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Pengaju</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Departemen</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Tanggal</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Score</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Status</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Reward</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $index => $ss)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all group shadow-sm border border-gray-100">
                        <td class="px-2 md:px-4 py-2 md:py-3 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500 text-xs md:text-sm">
                            {{ ($submissions->currentPage() - 1) * $submissions->perPage() + $loop->iteration }}
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            <span class="font-semibold text-gray-800 text-xs md:text-sm">{{ $ss->employee->nama ?? $ss->employee_npk }}</span>
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            <span class="text-gray-600 text-xs md:text-sm">{{ $ss->department_code }}</span>
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            @php
                                $relevantDate = match(request('status')) {
                                    'approved'     => $ss->final_approved_at,
                                    'rewarded'     => $ss->paid_at,
                                    'admin_review' => $ss->admin_approved_at,
                                    'kdp_review'   => $ss->kdp_approved_at,
                                    'spv_review'   => $ss->spv_approved_at,
                                    default        => $ss->submission_date,
                                };
                            @endphp
                            <span class="text-gray-600 text-xs md:text-sm">
                                {{ $relevantDate ? \Carbon\Carbon::parse($relevantDate)->format('d/m/Y') : '-' }}
                            </span>
                            @if(request('status') && $ss->submission_date && request('status') !== 'submitted')
                                <span class="block text-[9px] text-gray-400">Diajukan: {{ \Carbon\Carbon::parse($ss->submission_date)->format('d/m/Y') }}</span>
                            @endif
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            @if($ss->score !== null)
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-bold">{{ $ss->score }}</span>
                            @else
                                <span class="text-gray-400 italic text-xs">-</span>
                            @endif
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            @php
                                $statusColors = [
                                    'submitted' => 'yellow',
                                    'assessed' => 'blue',
                                    'spv_review' => 'purple',
                                    'kdp_review' => 'orange',
                                    'admin_review' => 'cyan',
                                    'approved' => 'green',
                                    'rejected' => 'red',
                                    'rewarded' => 'emerald'
                                ];
                                $color = $statusColors[$ss->status] ?? 'gray';
                            @endphp
                            <span class="bg-{{ $color }}-100 text-{{ $color }}-800 px-2 py-1 rounded-full text-xs font-semibold uppercase">
                                {{ str_replace('_', ' ', $ss->status) }}
                            </span>
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            @if($ss->reward_amount)
                                <span class="text-emerald-600 font-bold text-xs md:text-sm">Rp {{ number_format($ss->reward_amount, 0, ',', '.') }}</span>
                            @elseif($ss->calculated_reward_amount)
                                <span class="text-amber-600 font-bold text-xs md:text-sm">Rp {{ number_format($ss->calculated_reward_amount, 0, ',', '.') }}</span>
                                <span class="block text-[9px] text-gray-400">Belum dibayar</span>
                            @else
                                <span class="text-gray-400 italic text-xs">-</span>
                            @endif
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-1 md:gap-2">
                                <a href="{{ route('ss.admin.show', $ss->id) }}" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Detail">
                                    <i class="fa-solid fa-eye text-[8px] md:text-[10px]"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-gray-300 italic text-xs md:text-sm">Belum ada data pengajuan SS.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION AREA -->
        @if($submissions->hasPages())
        <div class="mt-4 md:mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-4 md:pt-6">
            <div class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $submissions->firstItem() ?? 0 }} to {{ $submissions->lastItem() ?? 0 }} of {{ $submissions->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $submissions->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<style>
    .period-preset { background: #fff; border-color: #e5e7eb; color: #64748b; }
    .period-preset:hover { border-color: #091E6E; color: #091E6E; background: #f8fafc; }
    .period-preset.active { background: #091E6E; border-color: #091E6E; color: #fff; box-shadow: 0 4px 6px -1px rgba(9, 30, 110, 0.2); }

    /* Styling Paging Horizontal */
    .custom-pagination nav { display: flex; align-items: center; justify-content: center; gap: 4px; }
    .custom-pagination nav svg { width: 0.875rem; height: 0.875rem; }
    @media (min-width: 768px) {
        .custom-pagination nav svg { width: 1rem; height: 1rem; }
    }
    .custom-pagination span[aria-current="page"] > span {
        background-color: #091E6E !important;
        color: white !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 4px 10px !important;
        font-size: 10px !important;
        font-weight: 800;
        box-shadow: 0 4px 6px -1px rgba(9, 30, 110, 0.2);
    }
    .custom-pagination a, .custom-pagination span {
        border-radius: 6px !important;
        padding: 4px 10px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        border: 1px solid #edf2f7 !important;
        color: #64748b;
        transition: all 0.2s ease;
    }
    @media (min-width: 768px) {
        .custom-pagination span[aria-current="page"] > span { padding: 6px 12px !important; font-size: 11px !important; }
        .custom-pagination a, .custom-pagination span { padding: 6px 12px !important; font-size: 11px !important; }
    }
    .custom-pagination a:hover { background-color: #f8fafc !important; border-color: #091E6E !important; color: #091E6E !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form          = document.getElementById('filterForm');
        const dateFromInput = document.getElementById('filterDateFrom');
        const dateToInput   = document.getElementById('filterDateTo');
        const pickerFrom    = document.getElementById('dateFromPicker');
        const pickerTo      = document.getElementById('dateToPicker');
        const clearBtn      = document.getElementById('clearPeriod');
        const presetButtons = document.querySelectorAll('.period-preset');

        // ── helpers ──────────────────────────────────────────────────────────
        function padNum(n) { return String(n).padStart(2, '0'); }

        function formatDate(d) {
            // d = Date object → "YYYY-MM-DD"
            return `${d.getFullYear()}-${padNum(d.getMonth() + 1)}-${padNum(d.getDate())}`;
        }

        function todayStr() {
            return formatDate(new Date());
        }

        function thisWeekRange() {
            const d = new Date();
            const day = d.getDay(); // 0=Sun
            const diffToMon = (day === 0) ? -6 : 1 - day;
            const mon = new Date(d);
            mon.setDate(d.getDate() + diffToMon);
            const sun = new Date(mon);
            sun.setDate(mon.getDate() + 6);
            return { from: formatDate(mon), to: formatDate(sun) };
        }

        function thisMonthRange() {
            const d = new Date();
            const first = new Date(d.getFullYear(), d.getMonth(), 1);
            const last  = new Date(d.getFullYear(), d.getMonth() + 1, 0);
            return { from: formatDate(first), to: formatDate(last) };
        }

        function lastMonthRange() {
            const d = new Date();
            const first = new Date(d.getFullYear(), d.getMonth() - 1, 1);
            const last  = new Date(d.getFullYear(), d.getMonth(), 0);
            return { from: formatDate(first), to: formatDate(last) };
        }

        function thisYearRange() {
            const y = new Date().getFullYear();
            return { from: `${y}-01-01`, to: `${y}-12-31` };
        }

        // ── apply a from/to date range and submit ─────────────────────────────
        function applyRange(from, to, shouldSubmit = true) {
            dateFromInput.value = from || '';
            dateToInput.value   = to   || '';
            pickerFrom.value    = from || '';
            pickerTo.value      = to   || '';

            // keep "to" >= "from"
            if (from && to && to < from) {
                pickerTo.value    = from;
                dateToInput.value = from;
            }

            updatePresetActive();
            if (shouldSubmit) form.submit();
        }

        // ── highlight matching preset ─────────────────────────────────────────
        function updatePresetActive() {
            const from = dateFromInput.value;
            const to   = dateToInput.value;

            presetButtons.forEach(btn => {
                btn.classList.remove('active');
                const preset = btn.dataset.preset;

                if (preset === 'all' && !from && !to) {
                    btn.classList.add('active');
                    return;
                }

                const today = todayStr();
                const week  = thisWeekRange();
                const month = thisMonthRange();
                const lm    = lastMonthRange();
                const yr    = thisYearRange();

                if (preset === 'today'      && from === today    && to === today)    btn.classList.add('active');
                if (preset === 'this_week'  && from === week.from && to === week.to) btn.classList.add('active');
                if (preset === 'this_month' && from === month.from && to === month.to) btn.classList.add('active');
                if (preset === 'last_month' && from === lm.from  && to === lm.to)   btn.classList.add('active');
                if (preset === 'this_year'  && from === yr.from  && to === yr.to)   btn.classList.add('active');
            });
        }

        // ── picker events ─────────────────────────────────────────────────────
        pickerFrom.addEventListener('change', function () {
            let from = this.value;
            let to   = pickerTo.value;
            if (!to || to < from) to = from;
            applyRange(from, to, true);
        });

        pickerTo.addEventListener('change', function () {
            let to   = this.value;
            let from = pickerFrom.value;
            if (!from || from > to) from = to;
            applyRange(from, to, true);
        });

        // ── preset buttons ────────────────────────────────────────────────────
        presetButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const preset = this.dataset.preset;

                if (preset === 'all') {
                    applyRange('', '', true);
                    return;
                }
                if (preset === 'today') {
                    const t = todayStr();
                    applyRange(t, t, true);
                    return;
                }
                if (preset === 'this_week') {
                    const r = thisWeekRange();
                    applyRange(r.from, r.to, true);
                    return;
                }
                if (preset === 'this_month') {
                    const r = thisMonthRange();
                    applyRange(r.from, r.to, true);
                    return;
                }
                if (preset === 'last_month') {
                    const r = lastMonthRange();
                    applyRange(r.from, r.to, true);
                    return;
                }
                if (preset === 'this_year') {
                    const r = thisYearRange();
                    applyRange(r.from, r.to, true);
                    return;
                }
            });
        });

        // ── clear button ──────────────────────────────────────────────────────
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                applyRange('', '', true);
            });
        }

        // ── init active state ─────────────────────────────────────────────────
        updatePresetActive();
    });
</script>
@endpush

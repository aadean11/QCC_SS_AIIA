@extends('welcome')

@section('title', 'Daftar Ide SS')

@section('content')
@php
    $dateFrom = request('date_from');
    $dateTo = request('date_to');

    if (!$dateFrom && !$dateTo && request()->filled('month') && request()->filled('year')) {
        $dateFrom = sprintf('%04d-%02d-01', (int) request('year'), (int) request('month'));
        $dateTo = \Carbon\Carbon::parse($dateFrom)->endOfMonth()->toDateString();
    }

    $periodDisplay = ($dateFrom && $dateTo)
        ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') . ' — ' . \Carbon\Carbon::parse($dateTo)->format('d/m/Y')
        : '';
    $minYear = min($years);
    $maxYear = max($years);
    $hasDateFilter = filled($dateFrom) || filled($dateTo);
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
            <input type="hidden" name="date_to" id="filterDateTo" value="{{ $dateTo }}">

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
                        <select name="status" onchange="this.form.submit()"
                            class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent w-full min-w-0">
                            <option value="">Semua Status</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="spv_review" {{ request('status') == 'spv_review' ? 'selected' : '' }}>Need SPV</option>
                            <option value="kdp_review" {{ request('status') == 'kdp_review' ? 'selected' : '' }}>KDP Review</option>
                            <option value="admin_review" {{ request('status') == 'admin_review' ? 'selected' : '' }}>Admin Review</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="rewarded" {{ request('status') == 'rewarded' ? 'selected' : '' }}>Rewarded</option>
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

            <!-- Date filter -->
            <div class="flex flex-col gap-2 pt-1">
                <label for="periodPicker" class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider pl-1">
                    Periode Tanggal
                </label>

                <div class="flex flex-wrap gap-2">
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

                <div class="relative flex items-center bg-white h-11 rounded-2xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E] focus-within:border-[#091E6E] focus-within:ring-2 focus-within:ring-[#091E6E]/20">
                    <i class="fa-regular fa-calendar-days text-[10px] text-gray-400 shrink-0 pl-3"></i>
                    <input type="text" id="periodPicker" value="{{ $periodDisplay }}" placeholder="Pilih rentang tanggal..."
                        readonly class="flex-1 min-w-0 py-2 pr-9 pl-2 text-[10px] md:text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer placeholder:font-medium placeholder:text-gray-400">
                    @if($hasDateFilter)
                        <button type="button" id="clearPeriod"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                            title="Hapus periode">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        </button>
                    @endif
                </div>
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
                            <span class="text-gray-600 text-xs md:text-sm">{{ \Carbon\Carbon::parse($ss->submission_date)->format('d/m/Y') }}</span>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.3/air-datepicker.css">
<style>
    .air-datepicker { font-family: 'Poppins', sans-serif; border-radius: 1rem; border: 1px solid #e5e7eb; box-shadow: 0 10px 25px -5px rgba(9, 30, 110, 0.15); }
    .air-datepicker-nav--title { font-weight: 700; color: #091E6E; }
    .air-datepicker-nav--action:hover { background: #eff6ff; }
    .air-datepicker-cell.-selected-, .air-datepicker-cell.-selected-.-focus- { background: #091E6E; }
    .air-datepicker-cell.-range-from-, .air-datepicker-cell.-range-to- { background: #091E6E; border-color: #091E6E; }
    .air-datepicker-cell.-in-range- { background: rgba(9, 30, 110, 0.12); color: #091E6E; }
    .air-datepicker-cell.-current- { color: #091E6E; border-color: #091E6E; }
    .air-datepicker-button { font-weight: 700; color: #091E6E; }

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

<script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.3/air-datepicker.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filterForm');
        const dateFromInput = document.getElementById('filterDateFrom');
        const dateToInput = document.getElementById('filterDateTo');
        const clearBtn = document.getElementById('clearPeriod');
        const presetButtons = document.querySelectorAll('.period-preset');

        const localeId = {
            days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            daysMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
            today: 'Hari ini',
            clear: 'Hapus',
            dateFormat: 'dd/MM/yyyy',
            timeFormat: 'HH:mm',
            firstDay: 1,
        };

        const selectedDates = @json(
            ($dateFrom && $dateTo)
                ? [$dateFrom, $dateTo]
                : []
        ).map((value) => new Date(value + 'T00:00:00'));

        function formatYmd(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function startOfMonth(date) {
            return new Date(date.getFullYear(), date.getMonth(), 1);
        }

        function endOfMonth(date) {
            return new Date(date.getFullYear(), date.getMonth() + 1, 0);
        }

        function getPresetRange(preset) {
            const now = new Date();

            if (preset === 'this_month') {
                return { from: startOfMonth(now), to: endOfMonth(now) };
            }

            if (preset === 'last_month') {
                const lastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                return { from: startOfMonth(lastMonth), to: endOfMonth(lastMonth) };
            }

            if (preset === 'this_year') {
                return {
                    from: new Date(now.getFullYear(), 0, 1),
                    to: new Date(now.getFullYear(), 11, 31),
                };
            }

            return null;
        }

        function updatePresetActive() {
            const from = dateFromInput.value;
            const to = dateToInput.value;

            presetButtons.forEach((button) => {
                button.classList.remove('active');
                const preset = button.dataset.preset;

                if (preset === 'all' && !from && !to) {
                    button.classList.add('active');
                    return;
                }

                const range = getPresetRange(preset);
                if (!range || !from || !to) return;

                if (formatYmd(range.from) === from && formatYmd(range.to) === to) {
                    button.classList.add('active');
                }
            });
        }

        function applyRange(fromDate, toDate, shouldSubmit = true) {
            dateFromInput.value = fromDate ? formatYmd(fromDate) : '';
            dateToInput.value = toDate ? formatYmd(toDate) : '';

            if (fromDate && toDate) {
                picker.selectDate([fromDate, toDate], { silent: true });
            } else {
                picker.clear({ silent: true });
            }

            updatePresetActive();

            if (shouldSubmit) {
                form.submit();
            }
        }

        const picker = new AirDatepicker('#periodPicker', {
            locale: localeId,
            range: true,
            multipleDatesSeparator: ' — ',
            dateFormat: 'dd/MM/yyyy',
            autoClose: true,
            selectedDates: selectedDates,
            minDate: new Date('{{ $minYear }}-01-01T00:00:00'),
            maxDate: new Date('{{ $maxYear }}-12-31T00:00:00'),
            buttons: ['clear'],
            onSelect({ date }) {
                if (!date || (Array.isArray(date) && date.length === 0)) {
                    applyRange(null, null, true);
                    return;
                }

                if (Array.isArray(date) && date.length === 2) {
                    applyRange(date[0], date[1], true);
                }
            },
        });

        presetButtons.forEach((button) => {
            button.addEventListener('click', function () {
                const preset = this.dataset.preset;

                if (preset === 'all') {
                    applyRange(null, null, true);
                    return;
                }

                const range = getPresetRange(preset);
                if (range) {
                    applyRange(range.from, range.to, true);
                }
            });
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                applyRange(null, null, true);
            });
        }

        updatePresetActive();
    });
</script>
@endpush
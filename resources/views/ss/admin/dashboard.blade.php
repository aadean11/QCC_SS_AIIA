@extends('welcome')

@section('title', 'Dashboard SS')

@section('content')
<div class="animate-reveal pb-20">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Monitoring SS', 'icon' => 'fa-regular fa-lightbulb'],
        'Dashboard SS',
    ]])

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-6">
        <div class="w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
            <div class="flex bg-gray-200/50 p-1 rounded-2xl w-fit mt-4 border border-gray-100 shadow-inner whitespace-nowrap">
                @if($isAdmin)
                    <button type="button" onclick="switchTab('company')" class="px-3 md:px-6 py-1.5 md:py-2 rounded-xl text-[9px] md:text-xs font-bold uppercase tracking-wider transition-all {{ $viewLevel == 'company' ? 'bg-white text-[#091E6E] shadow-sm' : 'text-gray-500 hover:text-[#091E6E]' }}">Company</button>
                @endif
                @if($isAdmin || $user->occupation === 'GMR')
                    <button type="button" onclick="switchTab('division')" class="px-3 md:px-6 py-1.5 md:py-2 rounded-xl text-[9px] md:text-xs font-bold uppercase tracking-wider transition-all {{ $viewLevel == 'division' ? 'bg-white text-[#091E6E] shadow-sm' : 'text-gray-500 hover:text-[#091E6E]' }}">Division</button>
                @endif
                <button type="button" onclick="switchTab('department')" class="px-3 md:px-6 py-1.5 md:py-2 rounded-xl text-[9px] md:text-xs font-bold uppercase tracking-wider transition-all {{ $viewLevel == 'department' ? 'bg-white text-[#091E6E] shadow-sm' : 'text-gray-500 hover:text-[#091E6E]' }}">Department</button>
            </div>
        </div>

        <form action="{{ route('ss.admin.dashboard') }}" method="GET" id="filterForm" class="flex flex-col md:flex-row gap-3 w-full md:w-auto flex-wrap">
            <input type="hidden" name="view_level" id="view_level" value="{{ $viewLevel }}">
            @if($viewLevel == 'department' && $isAdmin)
            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm">
                <i class="fa-solid fa-layer-group text-amber-500 text-xs"></i>
                <select name="division_code" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer">
                    <option value="">Semua Divisi...</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->code }}" {{ $selectedDiv == $div->code ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm">
                <i class="fa-regular fa-calendar text-blue-500 text-xs"></i>
                <select name="month" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm">
                <i class="fa-regular fa-calendar-alt text-blue-500 text-xs"></i>
                <select name="year" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-4 sm:mb-6 md:mb-8 text-left">
        <div class="glass-card py-2 px-3 sm:py-3 sm:px-4 md:py-4 md:px-6 rounded-[1.2rem] sm:rounded-[1.5rem] md:rounded-[2rem] shadow-sm border-l-4 border-blue-600 transition-all duration-300 hover:scale-[1.02] group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[7px] sm:text-[8px] md:text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">SS Terdaftar</p>
                    <div class="flex items-baseline gap-1">
                        <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-[#091E6E]">{{ $stats['total_ss'] }}</h3>
                        <span class="text-gray-400 font-bold text-[10px] sm:text-xs md:text-sm">/ {{ $stats['target_ss'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 h-0.5 sm:h-1 md:h-1.5 rounded-full mt-1 sm:mt-2 md:mt-3 overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-full transition-all duration-1000" style="width: {{ $stats['target_ss'] > 0 ? min(($stats['total_ss'] / $stats['target_ss']) * 100, 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-12 md:h-12 bg-blue-50 text-blue-600 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="fa-regular fa-lightbulb text-xs sm:text-sm md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="glass-card py-2 px-3 sm:py-3 sm:px-4 md:py-4 md:px-6 rounded-[1.2rem] sm:rounded-[1.5rem] md:rounded-[2rem] shadow-sm border-l-4 border-red-500 transition-all duration-300 hover:scale-[1.02] group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[7px] sm:text-[8px] md:text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Aktual Hari Ini</p>
                    <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-red-600">{{ $stats['actual_today'] }}</h3>
                    <p class="text-[6px] sm:text-[7px] md:text-[9px] text-red-400 mt-0.5 sm:mt-1 font-bold uppercase italic">{{ $todayDate }}</p>
                </div>
                <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-12 md:h-12 bg-red-50 text-red-600 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-red-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-calendar-day text-xs sm:text-sm md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="glass-card py-2 px-3 sm:py-3 sm:px-4 md:py-4 md:px-6 rounded-[1.2rem] sm:rounded-[1.5rem] md:rounded-[2rem] shadow-sm border-l-4 border-amber-500 transition-all duration-300 hover:scale-[1.02] group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[7px] sm:text-[8px] md:text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Need Review</p>
                    <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-amber-600">{{ $stats['need_review'] }}</h3>
                    <p class="text-[6px] sm:text-[7px] md:text-[9px] text-gray-400 mt-0.5 font-bold uppercase italic">Menunggu Approval</p>
                </div>
                <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-12 md:h-12 bg-amber-50 text-amber-600 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-amber-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-xs sm:text-sm md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="glass-card py-2 px-3 sm:py-3 sm:px-4 md:py-4 md:px-6 rounded-[1.2rem] sm:rounded-[1.5rem] md:rounded-[2rem] shadow-sm border-l-4 border-emerald-500 transition-all duration-300 hover:scale-[1.02] group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[7px] sm:text-[8px] md:text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">SS Selesai</p>
                    <div class="flex items-baseline gap-1">
                        <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-emerald-600">{{ $stats['completed'] }}</h3>
                        <span class="text-gray-400 font-bold text-[10px] sm:text-xs md:text-sm">/ {{ $stats['target_ss'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 h-0.5 sm:h-1 md:h-1.5 rounded-full mt-1 sm:mt-2 overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: {{ $stats['target_ss'] > 0 ? min(($stats['completed'] / $stats['target_ss']) * 100, 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-12 md:h-12 bg-emerald-50 text-emerald-600 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <i class="fa-solid fa-circle-check text-xs sm:text-sm md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    @php
        $gridClass = count($charts) > 1 ? 'lg:grid-cols-2' : 'grid-cols-1';
        $heightClass = count($charts) > 1 ? 'h-[220px] md:h-[280px]' : 'h-[220px] md:h-[320px]';
    @endphp

    <div class="grid grid-cols-1 {{ $gridClass }} gap-4 md:gap-8">
        @forelse($charts as $index => $chart)
        <div class="glass-card rounded-[1.5rem] md:rounded-[2.5rem] p-4 md:p-6 shadow-sm border border-white relative overflow-hidden">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-3">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-[#091E6E] uppercase tracking-tight">{{ $chart['title'] }}</h2>
                    <p class="text-[10px] md:text-xs text-gray-400 italic font-medium">{{ $months[$selectedMonth] }} {{ $selectedYear }}</p>
                </div>
                <div class="flex gap-2 md:gap-3 text-[7px] md:text-[9px] font-black uppercase tracking-widest">
                    <div class="flex items-center gap-1"><span class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-blue-100 shadow-sm"></span> Submitted</div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-blue-600 shadow-sm"></span> Approved</div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-emerald-500 shadow-sm"></span> Target</div>
                </div>
            </div>
            <div class="relative w-full {{ $heightClass }}">
                <canvas id="chart-{{ $index }}"></canvas>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center glass-card rounded-[1.5rem] md:rounded-[2.5rem]">
            <i class="fa-solid fa-chart-column text-4xl text-gray-200 mb-3"></i>
            <p class="text-gray-400 font-medium italic text-sm">Tidak ada data grafik untuk filter ini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    function switchTab(level) {
        document.getElementById('view_level').value = level;
        document.getElementById('filterForm').submit();
    }

    function countBarThickness(chartCount) {
        return chartCount > 1 ? 28 : 48;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const chartsData = @json($charts);
        Chart.register(ChartDataLabels);

        chartsData.forEach((chartItem, index) => {
            const canvas = document.getElementById('chart-' + index);
            if (!canvas) return;

            const d = chartItem.data;
            const submittedBars = [d.submitted[0] ?? 0, 0, 0];
            const approvedBars = [0, d.approved[1] ?? 0, d.approved[2] ?? 0];
            const barThickness = countBarThickness(chartsData.length);

            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: d.labels,
                    datasets: [
                        {
                            label: 'Target',
                            data: d.target,
                            type: 'line',
                            borderColor: '#10B981',
                            borderWidth: 3,
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            fill: false,
                            tension: 0,
                            order: 1,
                            datalabels: { display: false },
                        },
                        {
                            label: 'Submitted',
                            data: submittedBars,
                            backgroundColor: 'rgba(16, 53, 209, 0.2)',
                            borderRadius: 6,
                            barThickness: barThickness,
                            stack: 'ssStack',
                            order: 2,
                            datalabels: {
                                color: '#091E6E',
                                anchor: 'center',
                                align: 'center',
                            },
                        },
                        {
                            label: 'Approved',
                            data: approvedBars,
                            backgroundColor: '#1035D1',
                            borderRadius: 6,
                            barThickness: barThickness,
                            stack: 'ssStack',
                            order: 2,
                            datalabels: {
                                color: '#ffffff',
                                anchor: 'center',
                                align: 'center',
                            },
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#091E6E',
                            padding: 12,
                            cornerRadius: 10,
                            titleFont: { family: 'Poppins', size: 13, weight: 'bold' },
                            bodyFont: { family: 'Poppins', size: 12 },
                        },
                        datalabels: {
                            font: { family: 'Poppins', weight: 'bold', size: 10 },
                            formatter: function(value) { return value > 0 ? value : ''; },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            stacked: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                            ticks: {
                                stepSize: 1,
                                font: { family: 'Poppins', size: 11, weight: '600' },
                                color: '#94a3b8',
                            },
                        },
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Poppins', size: 10, weight: '700' },
                                color: '#64748b',
                            },
                        },
                    },
                },
            });
        });
    });
</script>
@endpush

@extends('welcome')

@section('title', 'Dashboard SS')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumbs -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-3 gap-4">
        <nav class="flex text-xs md:text-sm text-gray-400">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center text-gray-400">Monitoring SS</li>
                <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
                <li class="text-[#091E6E] font-semibold tracking-tight uppercase text-[10px] md:text-xs">Dashboard SS</li>
            </ol>
        </nav>
    </div>

    <!-- FILTER SECTION (bulan, tahun, departemen) -->
    <div class="flex flex-col md:flex-row justify-end items-start md:items-center mb-6 gap-4">
        <div class="w-full md:w-auto overflow-x-auto">
            <div class="flex bg-gray-200/50 p-1 rounded-2xl w-fit border border-gray-100 shadow-inner">
                <form method="GET" action="{{ route('ss.admin.dashboard') }}" id="filterForm" class="flex flex-col sm:flex-row gap-2 sm:gap-3 p-1">
                    <!-- Filter Departemen -->
                    <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-gray-200">
                        <i class="fa-regular fa-building text-blue-500 text-xs"></i>
                        <select name="department_code" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer">
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->code }}" {{ $selectedDept == $dept->code ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Filter Bulan -->
                    <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-gray-200">
                        <i class="fa-regular fa-calendar text-blue-500 text-xs"></i>
                        <select name="month" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Filter Tahun -->
                    <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-gray-200">
                        <i class="fa-regular fa-calendar-alt text-blue-500 text-xs"></i>
                        <select name="year" onchange="this.form.submit()" class="text-xs font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-4 sm:mb-6 md:mb-8 text-left">
        <!-- Total SS -->
        <div class="glass-card py-2 px-3 sm:py-3 sm:px-4 md:py-4 md:px-6 rounded-[1.2rem] sm:rounded-[1.5rem] md:rounded-[2rem] shadow-sm border-l-4 border-blue-600 transition-all duration-300 hover:scale-[1.02] md:hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[7px] sm:text-[8px] md:text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Total SS</p>
                    <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-[#091E6E]">{{ $total }}</h3>
                </div>
                <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-12 md:h-12 bg-blue-50 text-blue-600 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                    <i class="fa-regular fa-lightbulb text-xs sm:text-sm md:text-xl"></i>
                </div>
            </div>
            <i class="fa-regular fa-lightbulb absolute -right-2 -bottom-2 opacity-5 text-blue-600 text-3xl sm:text-4xl md:text-6xl"></i>
        </div>

        <!-- Belum Dinilai -->
        <div class="glass-card py-2 px-3 sm:py-3 sm:px-4 md:py-4 md:px-6 rounded-[1.2rem] sm:rounded-[1.5rem] md:rounded-[2rem] shadow-sm border-l-4 border-amber-500 transition-all duration-300 hover:scale-[1.02] md:hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[7px] sm:text-[8px] md:text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Belum Dinilai</p>
                    <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-amber-600">{{ $pendingScore }}</h3>
                </div>
                <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-12 md:h-12 bg-amber-50 text-amber-600 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-amber-600 group-hover:text-white transition-all duration-500">
                    <i class="fa-regular fa-hourglass-half text-xs sm:text-sm md:text-xl"></i>
                </div>
            </div>
            <i class="fa-regular fa-hourglass-half absolute -right-2 -bottom-2 opacity-5 text-amber-600 text-3xl sm:text-4xl md:text-6xl"></i>
        </div>

        <!-- Approved -->
        <div class="glass-card py-2 px-3 sm:py-3 sm:px-4 md:py-4 md:px-6 rounded-[1.2rem] sm:rounded-[1.5rem] md:rounded-[2rem] shadow-sm border-l-4 border-green-500 transition-all duration-300 hover:scale-[1.02] md:hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[7px] sm:text-[8px] md:text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Approved</p>
                    <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-green-600">{{ $approved }}</h3>
                </div>
                <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-12 md:h-12 bg-green-50 text-green-600 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-green-600 group-hover:text-white transition-all duration-500">
                    <i class="fa-regular fa-circle-check text-xs sm:text-sm md:text-xl"></i>
                </div>
            </div>
            <i class="fa-regular fa-circle-check absolute -right-2 -bottom-2 opacity-5 text-green-600 text-3xl sm:text-4xl md:text-6xl"></i>
        </div>

        <!-- Sudah Reward -->
        <div class="glass-card py-2 px-3 sm:py-3 sm:px-4 md:py-4 md:px-6 rounded-[1.2rem] sm:rounded-[1.5rem] md:rounded-[2rem] shadow-sm border-l-4 border-emerald-500 transition-all duration-300 hover:scale-[1.02] md:hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[7px] sm:text-[8px] md:text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Sudah Reward</p>
                    <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-emerald-600">{{ $rewarded }}</h3>
                </div>
                <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-12 md:h-12 bg-emerald-50 text-emerald-600 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                    <i class="fa-regular fa-money-bill-1 text-xs sm:text-sm md:text-xl"></i>
                </div>
            </div>
            <i class="fa-regular fa-money-bill-1 absolute -right-2 -bottom-2 opacity-5 text-emerald-600 text-3xl sm:text-4xl md:text-6xl"></i>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-8">
        <!-- Grafik Perkembangan SS per Bulan (dalam tahun terfilter) -->
        <div class="glass-card rounded-[1.5rem] md:rounded-[2.5rem] p-4 md:p-6 shadow-sm border border-white relative overflow-hidden">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-2">
                <h2 class="text-base md:text-lg font-bold text-[#091E6E] uppercase tracking-tight">Perkembangan SS per Bulan</h2>
                <p class="text-[10px] text-gray-400 italic">Tahun {{ $selectedYear }} @if($selectedDept) - {{ $departments->firstWhere('code', $selectedDept)->name ?? $selectedDept }} @endif</p>
            </div>
            <div class="relative w-full h-[250px] md:h-[300px]">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Distribusi Status SS (pada bulan & tahun terfilter) -->
        <div class="glass-card rounded-[1.5rem] md:rounded-[2.5rem] p-4 md:p-6 shadow-sm border border-white relative overflow-hidden">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-2">
                <h2 class="text-base md:text-lg font-bold text-[#091E6E] uppercase tracking-tight">Distribusi Status</h2>
                <p class="text-[10px] text-gray-400 italic">{{ $months[$selectedMonth] }} {{ $selectedYear }} @if($selectedDept) - {{ $departments->firstWhere('code', $selectedDept)->name ?? $selectedDept }} @endif</p>
            </div>
            <div class="relative w-full h-[250px] md:h-[300px]">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari controller
        const monthlyData = @json($monthlyData ?? []);
        const statusData = @json($statusData ?? []);

        // 1. Grafik Batang - Perkembangan SS per Bulan (tahun & departemen terfilter)
        if (monthlyData.length > 0) {
            const ctxBar = document.getElementById('monthlyChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [{
                        label: 'Jumlah SS',
                        data: monthlyData.map(item => item.total),
                        backgroundColor: 'rgba(16, 53, 209, 0.7)',
                        borderColor: '#1035D1',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
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
                            bodyFont: { family: 'Poppins', size: 12 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                            ticks: { 
                                stepSize: 1, 
                                font: { family: 'Poppins', size: 11, weight: '600' },
                                color: '#94a3b8' 
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { family: 'Poppins', size: 11, weight: '600' }, 
                                color: '#64748b'
                            }
                        }
                    }
                }
            });
        } else {
            const monthlyContainer = document.getElementById('monthlyChart').parentElement;
            monthlyContainer.innerHTML = '<div class="flex items-center justify-center h-full text-gray-400 italic">Tidak ada data untuk tahun ini</div>';
        }

        // 2. Grafik Pie - Distribusi Status (berdasarkan filter bulan, tahun, departemen)
        if (statusData.length > 0) {
            // Mapping warna per status (sesuai dengan badge status di seluruh sistem)
            const statusColorMap = {
                'submitted': '#F59E0B',
                'assessed': '#3B82F6',
                'spv_review': '#8B5CF6',
                'kdp_review': '#F97316',
                'approved': '#10B981',
                'rejected': '#EF4444',
                'rewarded': '#059669'
            };
            const labels = statusData.map(item => item.status_label ?? item.status);
            const values = statusData.map(item => item.count);
            const backgroundColors = statusData.map(item => statusColorMap[item.status] || '#6B7280');

            const ctxPie = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors,
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: 'Poppins', size: 11 },
                                color: '#475569',
                                usePointStyle: true,
                                boxWidth: 10
                            }
                        },
                        tooltip: {
                            backgroundColor: '#091E6E',
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            const statusContainer = document.getElementById('statusChart').parentElement;
            statusContainer.innerHTML = '<div class="flex items-center justify-center h-full text-gray-400 italic">Tidak ada data untuk periode ini</div>';
        }
    });
</script>
@endpush
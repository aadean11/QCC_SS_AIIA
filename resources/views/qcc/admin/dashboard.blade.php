@extends('welcome')

@section('title', 'Dashboard Admin QCC')

@section('content')
    <!-- Container Utama: Mendukung scrolling penuh dengan pb-20 -->
    <div class="animate-reveal pb-20 overflow-y-visible">
        
        <!-- Header & Multi-Filter Area -->
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
            <nav class="flex mb-6 text-sm text-gray-400">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">Monitoring QCC</li>
                    <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
                    <li class="text-[#091E6E] font-semibold tracking-tight">Dashboard Admin</li>
                </ol>
            </nav>

            <form action="{{ route('qcc.admin.dashboard') }}" method="GET" id="filterForm" class="flex flex-col md:flex-row gap-3 w-full xl:w-auto text-left">
                <!-- Filter Periode -->
                <div class="flex items-center gap-3 bg-white p-2 px-4 rounded-2xl shadow-sm border border-gray-100 transition-all hover:border-[#091E6E]">
                    <i class="fa-solid fa-calendar-check text-blue-400 text-xs"></i>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider text-nowrap">Periode:</span>
                    <select name="period_id" onchange="this.form.submit()" class="text-sm font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer min-w-[140px]">
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ $selectedPeriod == $p->id ? 'selected' : '' }}>
                                {{ $p->period_name }} ({{ $p->year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Departemen -->
                <div class="flex items-center gap-3 bg-white p-2 px-4 rounded-2xl shadow-sm border border-gray-100 transition-all hover:border-[#091E6E]">
                    <i class="fa-solid fa-building text-blue-400 text-xs"></i>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider text-nowrap">Dept:</span>
                    <select name="department_code" onchange="this.form.submit()" class="text-sm font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer min-w-[160px]">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->code }}" {{ $selectedDept == $dept->code ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Stat Cards Mini (Actual vs Target) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            
            <!-- Total Circle Card (Step 0 - Registrasi) -->
            <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-blue-600 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden text-left">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Circle Terdaftar</p>
                        <div class="flex items-baseline gap-1">
                            <h3 class="text-3xl font-black text-[#091E6E]">{{ $stats['total_circles'] }}</h3>
                            <span class="text-gray-400 font-bold text-sm">/ {{ $stats['target_circles'] }}</span>
                        </div>
                        <!-- Progress Bar Mini -->
                        <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3 overflow-hidden">
                            @php $percentReg = $stats['target_circles'] > 0 ? ($stats['total_circles'] / $stats['target_circles']) * 100 : 0; @endphp
                            <div class="bg-blue-600 h-full rounded-full transition-all duration-1000" style="width: {{ min($percentReg, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                </div>
                <i class="fa-solid fa-users absolute -right-2 -bottom-2 opacity-5 text-blue-600 text-6xl"></i>
            </div>

            <!-- Need Review Card -->
            <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-amber-500 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden text-left">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Need Review</p>
                        <h3 class="text-3xl font-black text-amber-600">{{ $stats['need_review'] }}</h3>
                        <p class="text-[9px] text-gray-400 mt-2 font-bold uppercase italic tracking-tighter">Menunggu Approval</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-amber-600 group-hover:text-white transition-all duration-500">
                        <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                    </div>
                </div>
                <i class="fa-solid fa-clock-rotate-left absolute -right-2 -bottom-2 opacity-5 text-amber-600 text-6xl"></i>
            </div>

            <!-- Completed Card (Step 8 - Selesai) -->
            <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-emerald-500 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden text-left">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Circle Selesai</p>
                        <div class="flex items-baseline gap-1">
                            <!-- Angka dinamis dibandingkan dengan Target -->
                            <h3 class="text-3xl font-black text-emerald-600">{{ $stats['completed'] }}</h3>
                            <span class="text-gray-400 font-bold text-sm">/ {{ $stats['target_circles'] }}</span>
                        </div>
                        <!-- Progress Bar Selesai -->
                        <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3 overflow-hidden">
                            @php $percentComp = $stats['target_circles'] > 0 ? ($stats['completed'] / $stats['target_circles']) * 100 : 0; @endphp
                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: {{ min($percentComp, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                    </div>
                </div>
                <i class="fa-solid fa-circle-check absolute -right-2 -bottom-2 opacity-5 text-emerald-600 text-6xl"></i>
            </div>

            <!-- Active Period Card -->
            <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-indigo-500 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden text-left">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Tahun Aktif</p>
                        <h3 class="text-3xl font-black text-indigo-600">{{ date('Y') }}</h3>
                        <p class="text-[9px] text-gray-400 mt-2 font-bold uppercase italic tracking-tight">Satu AISIN AIA</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                        <i class="fa-solid fa-calendar-days text-xl"></i>
                    </div>
                </div>
                <i class="fa-solid fa-calendar-days absolute -right-2 -bottom-2 opacity-5 text-indigo-600 text-6xl"></i>
            </div>
        </div>

        <!-- GRAFIK MONITORING -->
        <div class="glass-card rounded-[2.5rem] p-8 shadow-sm mb-10 border border-white relative">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 text-left">
                <div>
                    <h2 class="text-2xl font-bold text-[#091E6E]">Progress Activity Circle</h2>
                    <p class="text-sm text-gray-400 italic">
                        @php 
                            $deptName = $selectedDept ? $departments->where('code', $selectedDept)->first()->name : 'Semua Departemen';
                        @endphp
                        Data: <span class="font-bold text-[#1035D1] uppercase">{{ $deptName }}</span>
                    </p>
                </div>
                <!-- Legend -->
                <div class="flex flex-wrap gap-4 text-xs font-bold uppercase tracking-wider">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: rgba(16, 53, 209, 0.2);"></span>
                        <span class="text-gray-500 text-[10px]">Submitted</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#1035D1]"></span>
                        <span class="text-gray-500 text-[10px]">Approved</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-gray-500 text-[10px]">Target</span>
                    </div>
                </div>
            </div>

            <div class="relative w-full overflow-hidden" style="height: 350px;">
                <canvas id="qccProgressChart"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('qccProgressChart').getContext('2d');
        const chartData = @json($chartData);

        const labels = ['Step 0', 'Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5', 'Step 6', 'Step 7', 'Step 8'];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Target',
                        data: chartData.target,
                        type: 'line',
                        borderColor: '#10B981',
                        borderWidth: 3,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        fill: false,
                        tension: 0,
                        order: 1
                    },
                    {
                        label: 'Submitted (Actual)', // Biru Muda Pudar
                        data: chartData.submitted, 
                        backgroundColor: 'rgba(16, 53, 209, 0.2)', 
                        borderRadius: 6,
                        barThickness: 30, // Dibuat sedikit lebih tebal
                        order: 2
                    },
                    {
                        label: 'Approved (Selesai)', // Biru Tua Solid
                        data: chartData.approved, 
                        backgroundColor: '#1035D1', 
                        borderRadius: 6,
                        barThickness: 30,
                        order: 2
                    }
                ]
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
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw || 0;
                                if (context.datasetIndex === 1) {
                                    return 'Submitted: ' + value + ' Berkas';
                                } else if (context.datasetIndex === 2) {
                                    return 'Approved: ' + value + ' Berkas';
                                }
                                return label + ': ' + value;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: { stepSize: 1, font: { family: 'Poppins', size: 12, weight: '600' }, color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Poppins', size: 11, weight: '600' }, color: '#64748b' }
                    }
                }
            }
        });
    });
</script>
@endpush
@extends('welcome')

@section('title', 'Dashboard Admin QCC')

@section('content')
    <!-- Pastikan container utama memiliki padding-bottom agar tidak terpotong saat scroll -->
    <div class="animate-reveal pb-10">
        
        <!-- Header & Filter Area -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <!-- Breadcrumb -->
            <nav class="flex text-sm text-gray-400">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">Monitoring QCC</li>
                    <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
                    <li class="text-[#091E6E] font-semibold tracking-tight">Dashboard Admin</li>
                </ol>
            </nav>

            <!-- Filter Dropdown -->
            <form action="{{ route('qcc.admin.dashboard') }}" method="GET" id="filterDeptForm" class="w-full md:w-auto">
                <div class="flex items-center gap-3 bg-white p-2 px-4 rounded-2xl shadow-sm border border-gray-100">
                    <i class="fa-solid fa-filter text-gray-400 text-xs"></i>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Dept:</span>
                    <select name="department_code" onchange="this.form.submit()" 
                        class="text-sm font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer min-w-[150px]">
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

        <!-- Stat Cards Mini -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Total Circle -->
            <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-blue-600 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Total Circle</p>
                        <h3 class="text-3xl font-black text-[#091E6E]">{{ $stats['total_circles'] }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                </div>
                <!-- Dekorasi Background -->
                <div class="absolute -right-2 -bottom-2 opacity-5 text-blue-600 group-hover:opacity-10 transition-opacity">
                    <i class="fa-solid fa-users text-6xl"></i>
                </div>
            </div>

            <!-- Need Review -->
            <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-amber-500 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Need Review</p>
                        <h3 class="text-3xl font-black text-[#091E6E]">{{ $stats['need_review'] }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-amber-500 group-hover:text-white transition-all duration-500">
                        <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                    </div>
                </div>
                <div class="absolute -right-2 -bottom-2 opacity-5 text-amber-600 group-hover:opacity-10 transition-opacity">
                    <i class="fa-solid fa-clock-rotate-left text-6xl"></i>
                </div>
            </div>

            <!-- Completed -->
            <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-emerald-500 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Completed</p>
                        <h3 class="text-3xl font-black text-[#091E6E]">{{ $stats['completed'] }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                    </div>
                </div>
                <div class="absolute -right-2 -bottom-2 opacity-5 text-emerald-600 group-hover:opacity-10 transition-opacity">
                    <i class="fa-solid fa-circle-check text-6xl"></i>
                </div>
            </div>

            <!-- Periode Aktif -->
            <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-indigo-500 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Periode Aktif</p>
                        <h3 class="text-3xl font-black text-[#091E6E]">{{ $stats['active_periods'] }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                        <i class="fa-solid fa-calendar-days text-xl"></i>
                    </div>
                </div>
                <div class="absolute -right-2 -bottom-2 opacity-5 text-indigo-600 group-hover:opacity-10 transition-opacity">
                    <i class="fa-solid fa-calendar-days text-6xl"></i>
                </div>
            </div>
        </div>

        <!-- GRAFIK MONITORING -->
        <div class="glass-card rounded-[2.5rem] p-8 shadow-sm mb-10 border border-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-[#091E6E]">Progress Activity Circle</h2>
                    <p class="text-sm text-gray-400 italic">
                        @if($selectedDept) 
                            Menampilkan data untuk Departemen: <span class="font-bold text-[#1035D1]">{{ $departments->where('code', $selectedDept)->first()->name }}</span>
                        @else 
                            Menampilkan data akumulasi seluruh Departemen 
                        @endif
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

            <!-- Area Chart -->
            <div class="relative w-full overflow-x-auto" style="height: 380px;">
                <canvas id="qccProgressChart"></canvas>
            </div>
        </div>

        <!-- Monitoring Table -->
        <div class="glass-card rounded-[2.5rem] p-8 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-[#091E6E]">Monitoring Circle QCC</h2>
                <span class="text-[10px] bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-bold uppercase">Live Data</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-gray-400 text-[10px] uppercase tracking-widest font-bold">
                            <th class="px-6">Circle & Dept</th>
                            <th class="px-6 text-center">Progress Step (1-8)</th>
                            <th class="px-6">Status</th>
                            <th class="px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white hover:shadow-md transition-all rounded-2xl group border border-gray-100">
                            <td class="px-6 py-5 rounded-l-2xl leading-tight">
                                <p class="font-bold text-[#091E6E]">QCC BRAKE SYSTEM</p>
                                <p class="text-[10px] text-gray-400 uppercase font-medium">DEPT: PRODUCTION #1</p>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-center gap-1">
                                    @for($i=1; $i<=8; $i++)
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold 
                                            {{ $i <= 3 ? 'bg-emerald-500 text-white' : ($i == 4 ? 'bg-amber-400 text-white animate-pulse' : 'bg-gray-100 text-gray-300') }}">
                                            {{ $i }}
                                        </div>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-6 py-5"><span class="bg-amber-50 text-amber-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase border border-amber-100">Waiting Review</span></td>
                            <td class="px-6 py-5 rounded-r-2xl text-center text-blue-500">
                                <button class="p-2 hover:bg-blue-50 rounded-xl transition-colors">
                                    <i class="fa-solid fa-eye cursor-pointer"></i>
                                </button>
                            </td>
                        </tr>
                        <!-- Tambahkan baris dummy lain agar terlihat scrollable -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('qccProgressChart').getContext('2d');
        
        // Data dari PHP
        const chartData = @json($chartData);

        const labels = [
            ['0. Profile', "Aug '25"], 
            ['1. Tema', "Aug '25"], 
            ['2. Target', "Sept '25"], 
            ['3. Anakonda', "Sept '25"], 
            ['4. Anaseba', "Oct '25"], 
            ['5. Rencana', "Nov '25"], 
            ['6. Penanggulangan', "Dec '25"], 
            ['7. Evaluasi', "Jan '26"], 
            ['8. Standar', "Jan '26"]
        ];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Target',
                        data: [10, 10, 10, 10, 10, 10, 10, 10, 10],
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
                        label: 'Submitted',
                        data: chartData.submitted, 
                        backgroundColor: 'rgba(16, 53, 209, 0.2)', 
                        borderWidth: 0,
                        borderRadius: 5,
                        barThickness: 22,
                        categoryPercentage: 0.8,
                        barPercentage: 0.9,
                        order: 2
                    },
                    {
                        label: 'Approved',
                        data: chartData.approved, 
                        backgroundColor: '#1035D1', 
                        borderRadius: 5,
                        barThickness: 22,
                        categoryPercentage: 0.8,
                        barPercentage: 0.9,
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
                        titleFont: { family: 'Poppins', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.dataset.label + ': ' + context.raw + ' Circle';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: { stepSize: 5, font: { family: 'Poppins', size: 12, weight: '600' }, color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Poppins', size: 10, weight: '600' }, color: '#64748b' }
                    }
                }
            }
        });
    });
</script>
@endpush
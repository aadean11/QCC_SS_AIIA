@extends('welcome')

@section('title', 'Dashboard Admin QCC')

@section('content')
    <div class="animate-reveal">
        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-sm text-gray-400">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">Monitoring QCC</li>
                <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
                <li class="text-[#091E6E] font-semibold tracking-tight">Dashboard Admin</li>
            </ol>
        </nav>

        <!-- Stat Cards Mini -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="glass-card p-6 rounded-3xl shadow-sm border-l-4 border-blue-600">
                <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">Total Circle</p>
                <h3 class="text-2xl font-bold text-[#091E6E]">{{ $stats['total_circles'] }}</h3>
            </div>
            <div class="glass-card p-6 rounded-3xl shadow-sm border-l-4 border-amber-500">
                <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">Need Review</p>
                <h3 class="text-2xl font-bold text-[#091E6E]">{{ $stats['need_review'] }}</h3>
            </div>
            <div class="glass-card p-6 rounded-3xl shadow-sm border-l-4 border-emerald-500">
                <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">Completed</p>
                <h3 class="text-2xl font-bold text-[#091E6E]">{{ $stats['completed'] }}</h3>
            </div>
            <div class="glass-card p-6 rounded-3xl shadow-sm border-l-4 border-indigo-500">
                <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">Periode Aktif</p>
                <h3 class="text-2xl font-bold text-[#091E6E]">{{ $stats['active_periods'] }}</h3>
            </div>
        </div>

        <!-- GRAFIK MONITORING -->
        <div class="glass-card rounded-[2.5rem] p-8 shadow-sm mb-10 animate-reveal delay-1 border border-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-[#091E6E]">Progress Activity Circle</h2>
                    <p class="text-sm text-gray-400">Monitoring pencapaian langkah per periode (Actual vs Target)</p>
                </div>
                <!-- Legend Custom: Submitted (Kiri) - Approved (Kanan) -->
                <div class="flex flex-wrap gap-4 text-xs font-bold uppercase tracking-wider">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#93C5FD]"></span>
                        <span class="text-gray-500 text-[10px]">Submitted (Belum Approve)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#1035D1]"></span>
                        <span class="text-gray-500 text-[10px]">Approved (Selesai)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-gray-500 text-[10px]">Target</span>
                    </div>
                </div>
            </div>

            <!-- Area Chart -->
            <div class="relative w-full" style="height: 350px;">
                <canvas id="qccProgressChart"></canvas>
            </div>
        </div>

        <!-- Monitoring Table -->
        <div class="glass-card rounded-[2.5rem] p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-[#091E6E] mb-6">Monitoring Circle QCC</h2>
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
                            <td class="px-6 py-5"><span class="bg-amber-50 text-amber-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase border border-amber-100">Waiting Review Step 4</span></td>
                            <td class="px-6 py-5 rounded-r-2xl text-center text-blue-500"><i class="fa-solid fa-eye cursor-pointer hover:scale-110 transition"></i></td>
                        </tr>
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
                        label: 'Submitted', // BIRU MUDA (Kiri)
                        // Dummy: Menunjukkan proses yang sudah masuk tapi belum di-approve
                        data: [6, 8, 7, 10, 4, 9, 10, 12, 11], 
                        backgroundColor: '#93C5FD', 
                        borderRadius: 5,
                        barThickness: 22,
                        categoryPercentage: 0.8,
                        barPercentage: 0.9,
                        order: 2
                    },
                    {
                        label: 'Approved', // BIRU TUA (Kanan)
                        // Dummy: Menunjukkan proses yang sudah resmi selesai
                        data: [5, 5, 4, 9, 3, 10, 6, 5, 4], 
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
                                return context.dataset.label + ': ' + context.raw + ' Circle';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: { stepSize: 1, font: { family: 'Poppins', size: 12, weight: '600' }, color: '#94a3b8' }
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
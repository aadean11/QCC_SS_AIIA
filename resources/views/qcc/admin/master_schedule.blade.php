@extends('welcome')

@section('title', 'Master Schedule QCC')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-gray-400">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight uppercase text-xs">Master Schedule</li>
        </ol>
    </nav>

    <!-- Header & Filter -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Master Schedule QCC</h2>
            <p class="text-sm text-gray-400 italic">Visualisasi rentang waktu pengerjaan 8-Step PDCA berdasarkan periode.</p>
        </div>

        <form action="{{ route('qcc.admin.master_schedule') }}" method="GET" id="filterForm">
            <div class="flex items-center gap-3 bg-white p-2 px-4 rounded-2xl shadow-sm border border-gray-100 transition-all hover:border-[#091E6E]">
                <i class="fa-solid fa-calendar-days text-blue-400 text-xs"></i>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Periode:</span>
                <select name="period_id" onchange="this.form.submit()" class="text-sm font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer min-w-[140px]">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriod == $p->id ? 'selected' : '' }}>{{ $p->period_name }} ({{ $p->year }})</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Gantt Chart Container -->
    <div class="glass-card rounded-[2.5rem] p-4 md:p-10 shadow-sm border border-white relative overflow-hidden">
        <div class="flex justify-between items-center mb-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-timeline text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#091E6E]">{{ $period->period_name ?? 'N/A' }}</h3>
                    <p class="text-xs text-gray-400 font-medium">Timeline Duration: {{ \Carbon\Carbon::parse($period->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($period->end_date)->format('d M Y') }}</p>
                </div>
            </div>
            <!-- Legend Button -->
            <div class="hidden md:block">
                <span class="bg-red-50 text-red-500 px-4 py-2 rounded-xl text-[10px] font-bold uppercase border border-red-100">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> Real-time Tracking Active
                </span>
            </div>
        </div>

        <div class="relative w-full" style="min-height: 500px;">
            <canvas id="ganttChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Gunakan library Luxon untuk manajemen waktu di Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/luxon@3.3.0/build/global/luxon.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.3.1"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('ganttChart').getContext('2d');
        const rawData = @json($ganttData);
        
        // Format data untuk Floating Bar Chart
        const datasets = rawData.map(item => {
            return {
                label: item.step_name,
                data: [{
                    x: [item.start, item.end],
                    y: item.step_name
                }],
                backgroundColor: item.color,
                borderRadius: 10,
                borderSkipped: false,
                barPercentage: 0.6
            };
        });

        const config = {
            type: 'bar',
            data: {
                datasets: datasets
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const start = new Date(context.raw.x[0]).toLocaleDateString('id-ID', { day:'numeric', month:'short' });
                                const end = new Date(context.raw.x[1]).toLocaleDateString('id-ID', { day:'numeric', month:'short' });
                                return ` Durasi: ${start} - ${end}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'month',
                            displayFormats: { month: 'MMM yyyy' }
                        },
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: { font: { family: 'Poppins', size: 11 }, color: '#64748b' },
                        min: "{{ $period->start_date }}",
                        max: "{{ $period->end_date }}"
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { font: { family: 'Poppins', weight: 'bold', size: 12 }, color: '#091E6E' }
                    }
                }
            },
            plugins: [{
                id: 'todayLine',
                afterDraw: (chart) => {
                    const ctx = chart.ctx;
                    const xAxis = chart.scales.x;
                    const yAxis = chart.scales.y;
                    const xPos = xAxis.getPixelForValue(new Date());

                    if (xPos >= xAxis.left && xPos <= xAxis.right) {
                        ctx.save();
                        ctx.beginPath();
                        ctx.moveTo(xPos, yAxis.top);
                        ctx.lineTo(xPos, yAxis.bottom);
                        ctx.lineWidth = 2;
                        ctx.strokeStyle = '#ef4444';
                        ctx.setLineDash([6, 6]);
                        ctx.stroke();

                        // Label "Today"
                        ctx.fillStyle = '#ef4444';
                        ctx.font = 'bold 10px Poppins';
                        ctx.textAlign = 'center';
                        ctx.fillText('HARI INI', xPos, yAxis.top - 10);
                        ctx.restore();
                    }
                }
            }]
        };

        new Chart(ctx, config);
    });
</script>
@endpush
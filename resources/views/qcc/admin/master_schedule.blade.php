@extends('welcome')

@section('title', 'Master Schedule QCC')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumb -->
    <nav class="flex mb-4 md:mb-6 text-xs md:text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-gray-400">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight uppercase text-[10px] md:text-xs">Master Schedule</li>
        </ol>
    </nav>

    <!-- Header & Filter -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4 md:gap-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Master Schedule QCC</h2>
            <p class="text-xs md:text-sm text-gray-400 italic">Visualisasi rentang waktu pengerjaan 8-Step PDCA berdasarkan periode.</p>
        </div>

        <form action="{{ route('qcc.admin.master_schedule') }}" method="GET" id="filterForm" class="w-full md:w-auto">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 bg-white p-2 sm:p-2 sm:px-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 transition-all hover:border-[#091E6E] w-full md:w-auto">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <i class="fa-solid fa-calendar-days text-blue-400 text-[10px] md:text-xs"></i>
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest">Periode:</span>
                </div>
                <select name="period_id" onchange="this.form.submit()" class="text-xs md:text-sm font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer w-full sm:w-auto">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriod == $p->id ? 'selected' : '' }}>{{ $p->period_name }} ({{ $p->year }})</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Gantt Chart Container -->
    <div class="glass-card rounded-[1.5rem] md:rounded-[2.5rem] p-4 md:p-10 shadow-sm border border-white relative overflow-hidden">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 md:mb-10">
            <div class="flex items-center gap-3 md:gap-4 w-full sm:w-auto">
                <div class="w-8 h-8 md:w-12 md:h-12 bg-blue-50 text-blue-600 rounded-xl md:rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-timeline text-sm md:text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base md:text-xl font-bold text-[#091E6E]">{{ $period->period_name ?? 'N/A' }}</h3>
                    <p class="text-[10px] md:text-xs text-gray-400 font-medium">Timeline Duration: {{ \Carbon\Carbon::parse($period->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($period->end_date)->format('d M Y') }}</p>
                </div>
            </div>
            <!-- Legend Button - Sembunyikan di mobile, tampil di desktop -->
            <div class="hidden md:block">
                <span class="bg-red-50 text-red-500 px-4 py-2 rounded-xl text-[10px] font-bold uppercase border border-red-100">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> Real-time Tracking Active
                </span>
            </div>
        </div>

        <div class="relative w-full" style="min-height: 350px; height: 400px; max-height: 500px; @media (min-width: 768px) { min-height: 500px; }">
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
            data: { datasets: datasets },
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
                        time: { unit: 'month', displayFormats: { month: 'MMM yyyy' } },
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: { 
                            font: { family: 'Poppins', size: 10, weight: '500' }, 
                            color: '#64748b',
                            maxRotation: 45,
                            minRotation: 30
                        },
                        min: "{{ $period->start_date }}",
                        max: "{{ $period->end_date }}"
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { 
                            font: { family: 'Poppins', weight: 'bold', size: 11 }, 
                            color: '#091E6E'
                        }
                    }
                }
            },
            plugins: [{
                id: 'todayLine',
                afterDraw: (chart) => {
                    const { ctx, chartArea: { top, bottom, left, right }, scales: { x } } = chart;
                    const today = new Date();
                    const xPos = x.getPixelForValue(today);

                    if (xPos >= left && xPos <= right) {
                        ctx.save();
                        
                        // 1. Gambar Garis Vertikal Merah Putus-putus
                        ctx.beginPath();
                        ctx.moveTo(xPos, top);
                        ctx.lineTo(xPos, bottom);
                        ctx.lineWidth = 1.5;
                        ctx.strokeStyle = '#ef4444';
                        ctx.setLineDash([5, 5]);
                        ctx.stroke();

                        // 2. Desain Bendera Horizontal di Samping Kanan Garis
                        const dateText = today.toLocaleDateString('id-ID', { 
                            day: '2-digit', month: 'long', year: 'numeric' 
                        }).toUpperCase();
                        
                        ctx.font = 'bold 9px Poppins';
                        const textWidth = ctx.measureText(dateText).width;
                        const boxWidth = textWidth + 12; // Padding horizontal
                        const boxHeight = 18;
                        const boxX = xPos + 5; // Jarak 5px ke kanan dari garis merah
                        const boxY = top + 1;

                        // Gambar Background Kotak Bendera (Putih Ber-border Merah)
                        ctx.setLineDash([]); // Reset garis putus-putus jadi solid untuk kotak
                        ctx.fillStyle = '#ffffff';
                        ctx.strokeStyle = '#ef4444';
                        ctx.lineWidth = 1;
                        
                        // Efek Shadow halus agar terlihat seperti bendera melayang
                        ctx.shadowColor = 'rgba(0, 0, 0, 0.1)';
                        ctx.shadowBlur = 4;
                        ctx.shadowOffsetX = 2;

                        ctx.beginPath();
                        if (ctx.roundRect) {
                            ctx.roundRect(boxX, boxY, boxWidth, boxHeight, 5);
                        } else {
                            ctx.rect(boxX, boxY, boxWidth, boxHeight);
                        }
                        ctx.fill();
                        ctx.stroke();

                        // 3. Tulis Teks Tanggal (Warna Merah)
                        ctx.shadowBlur = 0; // Matikan shadow untuk teks
                        ctx.shadowOffsetX = 0;
                        ctx.fillStyle = '#ef4444';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(dateText, boxX + (boxWidth / 2), boxY + (boxHeight / 2));

                        ctx.restore();
                    }
                }
            }]
        };
        new Chart(ctx, config);
    });
</script>
@endpush
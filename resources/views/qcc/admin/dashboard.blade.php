@extends('welcome')

@section('title', 'Dashboard Admin QCC')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumbs -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-1 gap-4">
        <nav class="flex text-sm text-gray-400">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center text-gray-400">Monitoring QCC</li>
                <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
                <li class="text-[#091E6E] font-semibold tracking-tight uppercase text-xs">Dashboard Admin</li>
            </ol>
        </nav>
    </div>

    <!-- TAB NAVIGATION & FILTERS -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
        <div>
            <div class="flex bg-gray-200/50 p-1 rounded-2xl w-fit mt-4 border border-gray-100 shadow-inner">
                @if(session('active_role') === 'admin')
                    <button onclick="switchTab('company')" class="px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $viewLevel == 'company' ? 'bg-white text-[#091E6E] shadow-sm' : 'text-gray-500 hover:text-[#091E6E]' }}">Company</button>
                @endif

                @if(session('active_role') === 'admin' || $user->occupation === 'GMR')
                    <button onclick="switchTab('division')" class="px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $viewLevel == 'division' ? 'bg-white text-[#091E6E] shadow-sm' : 'text-gray-500 hover:text-[#091E6E]' }}">Division</button>
                @endif

                <button onclick="switchTab('department')" class="px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $viewLevel == 'department' ? 'bg-white text-[#091E6E] shadow-sm' : 'text-gray-500 hover:text-[#091E6E]' }}">Department</button>

                @if(in_array($user->occupation, ['KDP', 'SPV']))
                    <button onclick="switchTab('circle')" class="px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ $viewLevel == 'circle' ? 'bg-white text-[#091E6E] shadow-sm' : 'text-gray-500 hover:text-[#091E6E]' }}">
                       Circle
                    </button>
                @endif
            </div>
        </div>

        <form action="{{ route('qcc.admin.dashboard') }}" method="GET" id="filterForm" class="flex flex-col md:flex-row gap-3">
            <input type="hidden" name="view_level" id="view_level" value="{{ $viewLevel }}">
            @if($viewLevel == 'department' && session('active_role') === 'admin')
            <div class="flex items-center gap-3 bg-white p-2 px-4 rounded-2xl shadow-sm border border-gray-100 transition-all hover:border-[#091E6E]">
                <i class="fa-solid fa-layer-group text-amber-500 text-xs"></i>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Division:</span>
                <select name="division_code" onchange="this.form.submit()" class="text-sm font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer min-w-[150px]">
                    <option value="">Pilih Divisi...</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->code }}" {{ $selectedDiv == $div->code ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="flex items-center gap-3 bg-white p-2 px-4 rounded-2xl shadow-sm border border-gray-100 transition-all hover:border-[#091E6E]">
                <i class="fa-solid fa-calendar-check text-blue-400 text-xs"></i>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Periode:</span>
                <select name="period_id" onchange="this.form.submit()" class="text-sm font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer min-w-[140px]">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriod == $p->id ? 'selected' : '' }}>{{ $p->period_name }} ({{ $p->year }})</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 text-left">
        <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-blue-600 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Circle Terdaftar</p>
                    <div class="flex items-baseline gap-1"><h3 class="text-3xl font-black text-[#091E6E]">{{ $stats['total_circles'] }}</h3><span class="text-gray-400 font-bold text-sm">/ {{ $stats['target_circles'] }}</span></div>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-full transition-all duration-1000" style="width: {{ $stats['target_circles'] > 0 ? min(($stats['total_circles'] / $stats['target_circles']) * 100, 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-all duration-500"><i class="fa-solid fa-users text-xl"></i></div>
            </div>
            <i class="fa-solid fa-users absolute -right-2 -bottom-2 opacity-5 text-blue-600 text-6xl"></i>
        </div>
        <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-amber-500 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div><p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Need Review</p><h3 class="text-3xl font-black text-amber-600">{{ $stats['need_review'] }}</h3><p class="text-[9px] text-gray-400 mt-2 font-bold uppercase italic tracking-tighter">Menunggu Approval</p></div>
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-amber-600 group-hover:text-white transition-all duration-500"><i class="fa-solid fa-clock-rotate-left text-xl"></i></div>
            </div>
            <i class="fa-solid fa-clock-rotate-left absolute -right-2 -bottom-2 opacity-5 text-amber-600 text-6xl"></i>
        </div>
        <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-emerald-500 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Circle Selesai</p>
                    <div class="flex items-baseline gap-1"><h3 class="text-3xl font-black text-emerald-600">{{ $stats['completed'] }}</h3><span class="text-gray-400 font-bold text-sm">/ {{ $stats['target_circles'] }}</span></div>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: {{ $stats['target_circles'] > 0 ? min(($stats['completed'] / $stats['target_circles']) * 100, 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500"><i class="fa-solid fa-circle-check text-xl"></i></div>
            </div>
            <i class="fa-solid fa-circle-check absolute -right-2 -bottom-2 opacity-5 text-emerald-600 text-6xl"></i>
        </div>
        <div class="glass-card p-6 rounded-[2rem] shadow-sm border-l-4 border-indigo-500 transition-all duration-300 hover:scale-[1.05] hover:shadow-xl group relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div><p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Viewing Mode</p><h3 class="text-2xl font-black text-indigo-600 uppercase">{{ $viewLevel }}</h3><p class="text-[9px] text-gray-400 mt-2 font-bold uppercase italic tracking-tight">Satu AISIN AIIA</p></div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500"><i class="fa-solid fa-layer-group text-xl"></i></div>
            </div>
            <i class="fa-solid fa-layer-group absolute -right-2 -bottom-2 opacity-5 text-indigo-600 text-6xl"></i>
        </div>
    </div>

    <!-- DYNAMIC CHARTS GRID -->
    @php
        $gridClass = count($charts) > 1 ? 'lg:grid-cols-2' : 'grid-cols-1';
        $chartHeight = count($charts) > 1 ? '320px' : '450px';
    @endphp

    <div class="grid grid-cols-1 {{ $gridClass }} gap-8">
        @forelse($charts as $index => $chart)
        <div class="glass-card rounded-[2.5rem] p-8 shadow-sm border border-white relative overflow-hidden">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 text-left relative z-10">
                <div>
                    <h2 class="text-lg font-bold text-[#091E6E] uppercase tracking-tight">{{ $chart['title'] }}</h2>
                    <p class="text-xs text-gray-400 italic font-medium">Progress Activity Step 0 - 8</p>
                </div>
                <div class="flex gap-3 text-[9px] font-black uppercase tracking-widest">
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-100 shadow-sm"></span> Submited</div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600 shadow-sm"></span> Approved</div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm"></span> Target</div>
                </div>
            </div>
            <div class="relative w-full" style="height: {{ $chartHeight }};">
                <canvas id="chart-{{ $index }}"></canvas>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center glass-card rounded-[2.5rem]"><i class="fa-solid fa-chart-area text-5xl text-gray-200 mb-4"></i><p class="text-gray-400 font-medium italic">Tidak ada data grafik yang dapat ditampilkan.</p></div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    function switchTab(level) {
        document.getElementById('view_level').value = level;
        document.getElementById('filterForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inisialisasi Data Dasar (URUTAN PENTING: stepMonths harus pertama)
        const chartsData = @json($charts);
        const stepMonths = @json($stepMonths); 
        const progressLineX = {{ $progressLineX ?? 0 }};
        const todayLabel = "{{ $todayDate ?? '' }}";
        
        // 2. Buat label dua baris (Multi-line: Step & Bulan)
        const labels = [
            ['Step 0', stepMonths[0]],
            ['Step 1', stepMonths[1]],
            ['Step 2', stepMonths[2]],
            ['Step 3', stepMonths[3]],
            ['Step 4', stepMonths[4]],
            ['Step 5', stepMonths[5]],
            ['Step 6', stepMonths[6]],
            ['Step 7', stepMonths[7]],
            ['Step 8', stepMonths[8]]
        ];

        Chart.register(ChartDataLabels);

        // 3. Plugin Garis Merah "Current Time" (Posisi: Menempel di Sisi Kanan Bar Approved)
        const verticalLinePlugin = {
            id: 'verticalLine',
            afterDraw: (chart) => {
                if (chart.config.type === 'bar') {
                    const ctx = chart.ctx;
                    const yAxis = chart.scales.y;
                    
                    // Ambil metadata dari Dataset 2 (Approved / Biru Tua)
                    const meta = chart.getDatasetMeta(2); 
                    
                    // Ambil data batang sesuai index progressLineX dari Controller
                    const activeBar = meta.data[progressLineX];
                    
                    if (activeBar) {
                        // POSISI: Tepat di ujung kanan batang Approved
                        const xPos = activeBar.x + (activeBar.width / 2) + 2;

                        ctx.save();
                        
                        // 1. Gambar Garis Merah Putus-putus
                        ctx.beginPath();
                        ctx.setLineDash([5, 5]);
                        ctx.moveTo(xPos, yAxis.top);
                        ctx.lineTo(xPos, yAxis.bottom);
                        ctx.lineWidth = 1.5;
                        ctx.strokeStyle = 'rgba(239, 68, 68, 1)';
                        ctx.stroke();

                        // 2. Gambar Kotak Tanggal Vertikal
                        const text = todayLabel;
                        ctx.font = 'bold 10px Poppins';
                        const textWidth = ctx.measureText(text).width;
                        const boxWidth = textWidth + 10;
                        const boxHeight = 18;
                        const labelY = yAxis.top + 30;

                        ctx.translate(xPos, labelY);
                        ctx.rotate(-Math.PI / 2);
                        ctx.setLineDash([]);
                        ctx.fillStyle = '#ffffff';
                        ctx.strokeStyle = '#ef4444';
                        ctx.lineWidth = 1;
                        
                        const rectX = -boxWidth / 2;
                        const rectY = -boxHeight - 2;

                        ctx.beginPath();
                        if (ctx.roundRect) {
                            ctx.roundRect(rectX, rectY, boxWidth, boxHeight, 4);
                        } else {
                            ctx.rect(rectX, rectY, boxWidth, boxHeight);
                        }
                        ctx.fill();
                        ctx.stroke();

                        ctx.fillStyle = '#ef4444';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(text, 0, rectY + (boxHeight / 2));

                        ctx.restore();
                    }
                }
            }
        };

        Chart.register(verticalLinePlugin);

        // 4. Inisialisasi Chart
        chartsData.forEach((chart, index) => {
            const canvas = document.getElementById(`chart-${index}`);
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Target',
                            data: chart.data.target,
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
                            datalabels: { display: false }
                        },
                        {
                            label: 'Submitted',
                            data: chart.data.submitted,
                            backgroundColor: 'rgba(16, 53, 209, 0.2)',
                            borderRadius: 6,
                            barThickness: countBarThickness(chartsData.length),
                            order: 2,
                            datalabels: {
                                color: '#091E6E',
                                anchor: 'center',
                                align: 'center'
                            }
                        },
                        {
                            label: 'Approved',
                            data: chart.data.approved,
                            backgroundColor: '#1035D1',
                            borderRadius: 6,
                            barThickness: countBarThickness(chartsData.length),
                            order: 2,
                            datalabels: {
                                color: '#ffffff',
                                anchor: 'center',
                                align: 'center'
                            }
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
                            bodyFont: { family: 'Poppins', size: 12 }
                        },
                        datalabels: {
                            font: { family: 'Poppins', weight: 'bold', size: 10 },
                            formatter: function(value) { return value > 0 ? value : ''; }
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
                                font: { family: 'Poppins', size: 10, weight: '700' }, 
                                color: '#64748b',
                                autoSkip: false,
                                maxRotation: 0,
                                minRotation: 0
                            }
                        }
                    }
                }
            });
        });

        function countBarThickness(count) {
            return count > 1 ? 20 : 40;
        }
    });
</script>
@endpush
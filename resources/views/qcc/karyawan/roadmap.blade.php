@extends('welcome')

@section('title', 'Roadmap Progres Saya')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumb -->
    <nav class="flex mb-4 md:mb-6 text-xs md:text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-gray-400">
            <li class="inline-flex items-center">QCC Karyawan</li>
            <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold text-[10px] md:text-xs">Monitoring Roadmap Progres</li>
        </ol>
    </nav>

    <!-- Header & Filter -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Roadmap Aktivitas QCC</h2>
            <p class="text-xs md:text-sm text-gray-400 italic">Klik pada angka langkah untuk melihat dokumen yang telah diunggah.</p>
        </div>
        
        <form action="{{ route('qcc.karyawan.roadmap') }}" method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2 bg-white px-3 md:px-4 py-2 rounded-xl md:rounded-2xl border border-gray-100 shadow-sm transition-all hover:border-[#091E6E] w-full sm:w-auto">
                <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">Periode:</span>
                <select name="period_id" onchange="this.form.submit()" class="text-xs md:text-sm font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer w-full sm:w-auto">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriod == $p->id ? 'selected' : '' }}>{{ $p->period_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="relative w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Circle..." 
                    class="w-full pl-10 pr-4 py-2 bg-white border border-gray-100 rounded-xl md:rounded-2xl text-xs md:text-sm focus:ring-2 focus:ring-[#091E6E] outline-none shadow-sm">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] md:text-xs"></i>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[800px] md:min-w-full">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-2 md:px-4 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold">Circle & Tema Aktif</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold text-center">Roadmap Progress (Step 0-8)</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold rounded-tr-2xl">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($circles as $c)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100 group">
                        <td class="px-2 md:px-4 py-2 md:py-4 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500 text-xs md:text-sm">
                            @if($circles instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                {{ ($circles->currentPage() - 1) * $circles->perPage() + $loop->iteration }}
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-4 border-y border-gray-100">
                            <p class="font-bold text-[#091E6E] text-xs md:text-sm uppercase tracking-tight">{{ $c->circle_name }}</p>
                            <p class="text-[8px] md:text-[10px] text-gray-400 font-bold italic">{{ $c->activeTheme->theme_name ?? 'Belum ada tema aktif' }}</p>
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-4 border-y border-gray-100">
                            <div class="flex items-center justify-center gap-1 md:gap-1.5 flex-wrap">
                                
                                <!-- STEP 0 (DIAMBIL DARI m_qcc_circles) -->
                                @php
                                    $step0Color = 'bg-gray-100 text-gray-300 border-gray-200';
                                    $step0Click = '';
                                    if($c->step0_file_path) {
                                        if($c->status === 'ACTIVE') $step0Color = 'bg-emerald-500 text-white border-emerald-600 shadow-sm';
                                        elseif($c->status === 'WAITING SPV') $step0Color = 'bg-yellow-400 text-white border-yellow-500 animate-pulse';
                                        elseif($c->status === 'WAITING KDP') $step0Color = 'bg-blue-500 text-white border-blue-600';
                                        elseif(str_contains($c->status, 'REJECTED')) $step0Color = 'bg-red-500 text-white border-red-600';
                                        
                                        $fileUrl0 = asset('storage/' . $c->step0_file_path);
                                        $step0Click = "onclick=\"openFilePreview('$fileUrl0', 'Step 0 - Registration - $c->circle_name')\"";
                                    }
                                @endphp
                                <button {!! $step0Click !!} title="Step 0: Pendaftaran Circle ({{ $c->status }})"
                                    class="w-6 h-6 md:w-8 md:h-8 rounded-full border flex items-center justify-center text-[8px] md:text-[10px] font-black transition-all {{ $step0Color }} {{ $c->step0_file_path ? 'hover:scale-125 cursor-pointer' : 'cursor-default' }}">
                                    0
                                </button>

                                <!-- STEP 1 - 8 (DIAMBIL DARI t_qcc_circle_steps) -->
                                @for($i = 1; $i <= 8; $i++)
                                    @php
                                        $stepData = $c->activeTheme ? $c->activeTheme->stepProgress->where('qcc_step_id', $i)->first() : null;
                                        $colorClass = 'bg-gray-100 text-gray-300 border-gray-200'; 
                                        $btnClick = '';

                                        if($stepData) {
                                            if($stepData->status === 'APPROVED') $colorClass = 'bg-emerald-500 text-white border-emerald-600 shadow-sm';
                                            elseif($stepData->status === 'WAITING SPV') $colorClass = 'bg-yellow-400 text-white border-yellow-500 animate-pulse';
                                            elseif($stepData->status === 'WAITING KDP') $colorClass = 'bg-blue-500 text-white border-blue-600';
                                            elseif(str_contains($stepData->status, 'REJECTED')) $colorClass = 'bg-red-500 text-white border-red-600';

                                            $fileUrl = asset('storage/' . $stepData->file_path);
                                            $btnClick = "onclick=\"openFilePreview('$fileUrl', 'Step $i - $c->circle_name')\"";
                                        }
                                    @endphp
                                    <button {!! $btnClick !!} title="Step {{ $i }}: {{ $stepData->status ?? 'Belum ada data' }}"
                                        class="w-6 h-6 md:w-8 md:h-8 rounded-full border flex items-center justify-center text-[8px] md:text-[10px] font-black transition-all {{ $colorClass }} {{ $stepData ? 'hover:scale-125 cursor-pointer' : 'cursor-default' }}">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </td>
                        
                        <td class="px-3 md:px-6 py-2 md:py-4 rounded-r-xl border-y border-r border-gray-100 text-center">
                            @if($c->activeTheme)
                                <a href="{{ route('qcc.karyawan.progress', ['theme_id' => $c->activeTheme->id]) }}" 
                                    class="inline-flex items-center gap-2 bg-[#091E6E] text-white px-2 md:px-4 py-1 md:py-1.5 rounded-lg text-[8px] md:text-[10px] font-bold hover:bg-[#130998] transition-all shadow-md">
                                    <i class="fa-solid fa-cloud-arrow-up text-[9px] md:text-xs"></i> UPDATE
                                </a>
                            @else
                                <span class="text-[8px] md:text-[10px] text-gray-400 italic font-bold">SET TEMA DULU</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-10 md:py-20 text-gray-300 italic text-xs md:text-sm">Anda belum terdaftar dalam Circle manapun pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend Info (Tetap Sama) -->
    <div class="flex flex-wrap gap-2 md:gap-4 justify-center mt-6 md:mt-8 bg-white/50 p-3 md:p-4 rounded-xl md:rounded-2xl border border-white">
        <div class="flex items-center gap-1 md:gap-2 text-[7px] md:text-[9px] font-bold text-gray-500 uppercase"><span class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-gray-100 border"></span> Belum Upload</div>
        <div class="flex items-center gap-1 md:gap-2 text-[7px] md:text-[9px] font-bold text-gray-500 uppercase"><span class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-yellow-400"></span> Menunggu SPV</div>
        <div class="flex items-center gap-1 md:gap-2 text-[7px] md:text-[9px] font-bold text-gray-500 uppercase"><span class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-blue-500"></span> Menunggu KDP</div>
        <div class="flex items-center gap-1 md:gap-2 text-[7px] md:text-[9px] font-bold text-gray-500 uppercase"><span class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-emerald-500"></span> Approved</div>
        <div class="flex items-center gap-1 md:gap-2 text-[7px] md:text-[9px] font-bold text-gray-500 uppercase"><span class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-red-500"></span> Rejected</div>
    </div>
</div>

<!-- Modal Preview PDF (Tetap Sama) -->
<div id="modalFilePreview" class="fixed inset-0 z-[110] hidden overflow-y-auto bg-black/70 backdrop-blur-md">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-5xl h-[90vh] shadow-2xl animate-reveal overflow-hidden flex flex-col">
            <div class="sidebar-gradient p-3 md:p-5 text-white flex justify-between items-center">
                <div class="flex items-center gap-2 md:gap-3">
                    <i class="fa-solid fa-file-pdf text-lg md:text-xl"></i>
                    <h3 id="previewTitle" class="text-xs md:text-sm font-bold uppercase tracking-widest text-white">Preview Dokumen</h3>
                </div>
                <button onclick="closeModal('modalFilePreview')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <iframe id="fileIframe" src="" class="flex-1 w-full border-none bg-gray-100"></iframe>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openFilePreview(url, title) {
        document.getElementById('fileIframe').src = url;
        document.getElementById('previewTitle').innerText = title;
        document.getElementById('modalFilePreview').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
        if(id === 'modalFilePreview') document.getElementById('fileIframe').src = '';
    }
</script>
@endpush
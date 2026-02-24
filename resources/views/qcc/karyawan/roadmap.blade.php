@extends('welcome')

@section('title', 'Roadmap Progres Saya')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-gray-400">
            <li class="inline-flex items-center">QCC Karyawan</li>
            <li><i class="fa-solid fa-chevron-right text-[10px] mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold text-xs">Monitoring Roadmap Progres</li>
        </ol>
    </nav>

    <!-- Header & Filter -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-[#091E6E]">Roadmap Aktivitas QCC</h2>
            <p class="text-sm text-gray-400 italic">Klik pada angka langkah untuk melihat dokumen yang telah diunggah.</p>
        </div>
        
        <form action="{{ route('qcc.karyawan.roadmap') }}" method="GET" class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-2xl border border-gray-100 shadow-sm transition-all hover:border-[#091E6E]">
                <span class="text-[10px] font-bold text-gray-400 uppercase">Periode:</span>
                <select name="period_id" onchange="this.form.submit()" class="text-sm font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer min-w-[120px]">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriod == $p->id ? 'selected' : '' }}>{{ $p->period_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Circle..." class="pl-10 pr-4 py-2 bg-white border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-[#091E6E] outline-none shadow-sm">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[2rem] p-6 shadow-sm border border-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <!-- Tambah Kolom No -->
                        <th class="px-4 py-4 text-white text-[10px] uppercase tracking-widest font-bold rounded-tl-2xl text-center w-12">No</th>
                        <!-- Hapus rounded-tl-2xl dari Circle & Tema -->
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold">Circle & Tema Aktif</th>
                        <th class="px-6 py-4 text-white text-[10px] uppercase tracking-widest font-bold text-center">Roadmap Progress (Step 1-8)</th>
                        <th class="px-6 py-4 text-center text-white text-[10px] uppercase tracking-widest font-bold rounded-tr-2xl">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($circles as $c)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100 group">
                        <!-- Kolom Nomor Urut -->
                        <td class="px-4 py-4 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500">
                            {{-- Gunakan logika ini jika menggunakan pagination di Controller --}}
                            @if($circles instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                {{ ($circles->currentPage() - 1) * $circles->perPage() + $loop->iteration }}
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </td>

                        <!-- Kolom Info Circle (Hapus rounded-l-xl dan border-l karena pindah ke No) -->
                        <td class="px-6 py-4 border-y border-gray-100">
                            <p class="font-bold text-[#091E6E] text-sm uppercase tracking-tight">{{ $c->circle_name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold italic">{{ $c->activeTheme->theme_name ?? 'Belum ada tema aktif' }}</p>
                        </td>

                        <td class="px-6 py-4 border-y border-gray-100">
                            <div class="flex items-center justify-center gap-1.5">
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
                                        class="w-8 h-8 rounded-full border flex items-center justify-center text-[10px] font-black transition-all {{ $colorClass }} {{ $stepData ? 'hover:scale-125 cursor-pointer' : 'cursor-default' }}">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 rounded-r-xl border-y border-r border-gray-100 text-center">
                            @if($c->activeTheme)
                                <a href="{{ route('qcc.karyawan.progress', ['theme_id' => $c->activeTheme->id]) }}" 
                                    class="inline-flex items-center gap-2 bg-[#091E6E] text-white px-4 py-1.5 rounded-lg text-[10px] font-bold hover:bg-[#130998] transition-all shadow-md">
                                    <i class="fa-solid fa-cloud-arrow-up"></i> UPDATE
                                </a>
                            @else
                                <span class="text-[10px] text-gray-400 italic">Set Tema Terlebih Dahulu</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <!-- Update colspan dari 3 menjadi 4 -->
                        <td colspan="4" class="text-center py-20 text-gray-300 italic">Anda belum terdaftar dalam Circle manapun pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend Info -->
    <div class="flex flex-wrap gap-4 justify-center mt-8 bg-white/50 p-4 rounded-2xl border border-white">
        <div class="flex items-center gap-2 text-[9px] font-bold text-gray-500 uppercase"><span class="w-3 h-3 rounded-full bg-gray-100 border"></span> Belum Upload</div>
        <div class="flex items-center gap-2 text-[9px] font-bold text-gray-500 uppercase"><span class="w-3 h-3 rounded-full bg-yellow-400"></span> Menunggu SPV</div>
        <div class="flex items-center gap-2 text-[9px] font-bold text-gray-500 uppercase"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Menunggu KDP</div>
        <div class="flex items-center gap-2 text-[9px] font-bold text-gray-500 uppercase"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Approved</div>
        <div class="flex items-center gap-2 text-[9px] font-bold text-gray-500 uppercase"><span class="w-3 h-3 rounded-full bg-red-500"></span> Rejected</div>
    </div>
</div>

<!-- Modal Preview PDF -->
<div id="modalFilePreview" class="fixed inset-0 z-[110] hidden overflow-y-auto bg-black/70 backdrop-blur-md">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-[2rem] w-full max-w-5xl h-[90vh] shadow-2xl animate-reveal overflow-hidden flex flex-col">
            <div class="sidebar-gradient p-5 text-white flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-file-pdf text-xl"></i>
                    <h3 id="previewTitle" class="text-sm font-bold uppercase tracking-widest text-white">Preview Dokumen</h3>
                </div>
                <button onclick="closeModal('modalFilePreview')" class="text-white/70 hover:text-white text-2xl">&times;</button>
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
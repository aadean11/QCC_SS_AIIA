@extends('welcome')

@section('title', 'Monitoring Progres Keseluruhan')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumb -->
    <nav class="flex mb-4 md:mb-6 text-xs md:text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-gray-400">
            <li class="inline-flex items-center">Monitoring QCC</li>
            <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold text-[10px] md:text-xs">Seluruh Progres Circle</li>
        </ol>
    </nav>

    <!-- Filter Area -->
    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 mb-6 md:mb-8 border border-white shadow-sm">
        <form action="{{ route('qcc.admin.all_progress') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4 items-end">
            <div>
                <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase ml-2">Periode</label>
                <select name="period_id" onchange="this.form.submit()" class="w-full mt-1 p-2 md:p-2.5 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl text-xs md:text-sm font-bold text-[#091E6E]">
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" {{ $selectedPeriod == $p->id ? 'selected' : '' }}>{{ $p->period_name }} ({{ $p->year }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase ml-2">Divisi</label>
                <select name="division_code" onchange="this.form.submit()" class="w-full mt-1 p-2 md:p-2.5 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl text-xs md:text-sm font-bold text-[#091E6E]">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->code }}" {{ $selectedDiv == $div->code ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase ml-2">Departemen</label>
                <select name="department_code" onchange="this.form.submit()" class="w-full mt-1 p-2 md:p-2.5 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl text-xs md:text-sm font-bold text-[#091E6E]">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->code }}" {{ $selectedDept == $dept->code ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Circle..." class="w-full pl-8 md:pl-10 pr-4 py-2 md:py-2.5 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl text-xs md:text-sm focus:ring-2 focus:ring-[#091E6E] outline-none">
                <i class="fa-solid fa-magnifying-glass absolute left-3 md:left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs md:text-sm"></i>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0"> <!-- Agar scroll tidak terpotong padding di mobile -->
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[900px] md:min-w-full"> <!-- Minimum width untuk mobile -->
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-2 md:px-4 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold">Circle & Tema</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold">Departemen</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold text-center">Roadmap Progress (Step 1-8)</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center text-white text-[8px] md:text-[10px] uppercase tracking-widest font-bold rounded-tr-2xl">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($circles as $c)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100 group">
                        <td class="px-2 md:px-4 py-3 md:py-4 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500 text-xs md:text-sm">
                            {{ ($circles->currentPage() - 1) * $circles->perPage() + $loop->iteration }}
                        </td>

                        <td class="px-3 md:px-6 py-3 md:py-4 border-y border-gray-100">
                            <p class="font-bold text-[#091E6E] text-xs md:text-sm uppercase tracking-tight">{{ $c->circle_name }}</p>
                            <p class="text-[8px] md:text-[10px] text-gray-400 font-bold italic">{{ $c->activeTheme->theme_name ?? 'Tema belum diset' }}</p>
                        </td>

                        <td class="px-3 md:px-6 py-3 md:py-4 border-y border-gray-100">
                            <span class="text-[10px] md:text-xs font-bold text-gray-600 uppercase">{{ $c->department->name ?? $c->department_code }}</span>
                        </td>

                        <td class="px-3 md:px-6 py-3 md:py-4 border-y border-gray-100">
                            <div class="flex items-center justify-center gap-1 md:gap-1.5 flex-wrap">
                                @for($i = 1; $i <= 8; $i++)
                                    @php
                                        $stepData = $c->activeTheme ? $c->activeTheme->stepProgress->where('qcc_step_id', $i)->first() : null;
                                        $colorClass = 'bg-gray-100 text-gray-300 border-gray-200'; 
                                        $btnClick = '';

                                        if($stepData) {
                                            if($stepData->status === 'APPROVED') $colorClass = 'bg-emerald-500 text-white border-emerald-600';
                                            elseif($stepData->status === 'WAITING SPV') $colorClass = 'bg-yellow-400 text-white border-yellow-500 animate-pulse';
                                            elseif($stepData->status === 'WAITING KDP') $colorClass = 'bg-blue-500 text-white border-blue-600';
                                            elseif(str_contains($stepData->status, 'REJECTED')) $colorClass = 'bg-red-500 text-white border-red-600';

                                            $fileUrl = asset('storage/' . $stepData->file_path);
                                            $btnClick = "onclick=\"openFilePreview('$fileUrl', 'Step $i - $c->circle_name')\"";
                                        }
                                    @endphp
                                    <button {!! $btnClick !!} title="Step {{ $i }}: {{ $stepData->status ?? 'Belum ada data' }}"
                                        class="w-6 h-6 md:w-7 md:h-7 rounded-full border flex items-center justify-center text-[8px] md:text-[10px] font-black transition-all {{ $colorClass }} {{ $stepData ? 'hover:scale-125 cursor-pointer shadow-sm' : 'cursor-default' }}">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </td>

                        <td class="px-3 md:px-6 py-3 md:py-4 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <a href="{{ route('qcc.admin.dashboard', ['view_level' => 'circle', 'department_code' => $c->department_code]) }}" 
                                class="inline-flex items-center gap-1 md:gap-2 bg-blue-50 text-blue-600 px-2 md:px-3 py-1 md:py-1.5 rounded-lg text-[8px] md:text-[10px] font-bold hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                <i class="fa-solid fa-chart-pie text-xs"></i> ANALYTIC
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 md:py-20 text-gray-300 italic text-xs md:text-sm">Data Circle tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 md:mt-6">
            {{ $circles->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<!-- Modal Preview PDF -->
<div id="modalFilePreview" class="fixed inset-0 z-[110] hidden overflow-y-auto bg-black/70 backdrop-blur-md">
    <div class="flex items-center justify-center min-h-screen p-2 md:p-4">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] w-full max-w-5xl h-[90vh] shadow-2xl animate-reveal overflow-hidden flex flex-col">
            <div class="sidebar-gradient p-3 md:p-5 text-white flex justify-between items-center">
                <h3 id="previewTitle" class="text-[10px] md:text-sm font-bold uppercase tracking-widest">Preview Dokumen</h3>
                <button onclick="closeModal('modalFilePreview')" class="text-white/70 hover:text-white text-xl md:text-2xl">&times;</button>
            </div>
            <iframe id="fileIframe" src="" class="flex-1 w-full border-none"></iframe>
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
@extends('welcome')

@section('title', 'Detail SS')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumb -->
    <nav class="flex mb-4 md:mb-6 text-xs md:text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">SS</li>
            <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight text-[10px] md:text-xs">Detail SS</li>
        </ol>
    </nav>

    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-white overflow-hidden">
        <!-- Header Card dengan Gradient -->
        <div class="sidebar-gradient px-4 md:px-8 py-4 md:py-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h2 class="text-white text-lg md:text-2xl font-bold tracking-tight">Detail Suggestion System</h2>
                <p class="text-blue-200 text-[10px] md:text-xs mt-1">Informasi lengkap pengajuan SS</p>
            </div>
            <a href="{{ route('ss.karyawan.index') }}" class="bg-white/10 hover:bg-white/20 text-white text-xs md:text-sm font-semibold py-2 px-4 rounded-xl transition-all flex items-center gap-2 backdrop-blur-sm border border-white/20">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>

        <!-- Body Card -->
        <div class="p-4 md:p-8 space-y-6 md:space-y-8">
            <!-- Informasi Pengajuan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div class="bg-gray-50/50 p-3 md:p-4 rounded-xl border border-gray-100">
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Upload</span>
                    <p class="text-sm md:text-base font-semibold text-gray-800 mt-1">{{ \Carbon\Carbon::parse($submission->submission_date)->format('d/m/Y H:i') }}</p>
                </div>
                <div class="bg-gray-50/50 p-3 md:p-4 rounded-xl border border-gray-100">
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Departemen</span>
                    <p class="text-sm md:text-base font-semibold text-gray-800 mt-1">{{ $submission->department_code ?? '-' }}</p>
                </div>
                <div class="bg-gray-50/50 p-3 md:p-4 rounded-xl border border-gray-100">
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">File PDF</span>
                    <p class="mt-1"><a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-2 text-sm md:text-base"><i class="fa-regular fa-file-pdf text-lg"></i> Buka PDF</a></p>
                </div>
                <div class="bg-gray-50/50 p-3 md:p-4 rounded-xl border border-gray-100">
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</span>
                    @php
                        $statusColors = ['draft'=>'gray','submitted'=>'yellow','assessed'=>'blue','spv_review'=>'purple','kdp_review'=>'orange','approved'=>'green','rejected'=>'red','rewarded'=>'emerald'];
                        $color = $statusColors[$submission->status] ?? 'gray';
                    @endphp
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold uppercase bg-{{ $color }}-100 text-{{ $color }}-800">
                        {{ str_replace('_', ' ', $submission->status) }}
                    </span>
                </div>
            </div>

            <!-- Penilaian -->
            <div>
                <h3 class="text-sm md:text-base font-bold text-[#091E6E] border-l-4 border-[#091E6E] pl-3 mb-4">Penilaian</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div class="bg-gradient-to-r from-blue-50 to-white p-4 rounded-xl border border-blue-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Score</span>
                        <p class="text-3xl md:text-4xl font-black {{ $submission->score ? 'text-blue-600' : 'text-gray-400' }} mt-1">{{ $submission->score ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Catatan SS</span>
                        <p class="text-sm md:text-base text-gray-700 mt-1">{{ $submission->notes ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Review SPV -->
            <div>
                <h3 class="text-sm md:text-base font-bold text-[#091E6E] border-l-4 border-[#091E6E] pl-3 mb-4">Review SPV</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</span>
                        <div class="mt-1">
                            @if($submission->spv_status == 'approved')
                                <span class="inline-flex items-center gap-1 text-green-600 font-semibold text-sm"><i class="fa-regular fa-circle-check"></i> Approved</span>
                            @elseif($submission->spv_status == 'rejected')
                                <span class="inline-flex items-center gap-1 text-red-600 font-semibold text-sm"><i class="fa-regular fa-circle-xmark"></i> Rejected</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-gray-500 text-sm"><i class="fa-regular fa-clock"></i> Pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Catatan SPV</span>
                        <p class="text-sm md:text-base text-gray-700 mt-1">{{ $submission->spv_notes ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Review KDP -->
            <div>
                <h3 class="text-sm md:text-base font-bold text-[#091E6E] border-l-4 border-[#091E6E] pl-3 mb-4">Review KDP</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</span>
                        <div class="mt-1">
                            @if($submission->kdp_status == 'approved')
                                <span class="inline-flex items-center gap-1 text-green-600 font-semibold text-sm"><i class="fa-regular fa-circle-check"></i> Approved</span>
                            @elseif($submission->kdp_status == 'rejected')
                                <span class="inline-flex items-center gap-1 text-red-600 font-semibold text-sm"><i class="fa-regular fa-circle-xmark"></i> Rejected</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-gray-500 text-sm"><i class="fa-regular fa-clock"></i> Pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Catatan KDP</span>
                        <p class="text-sm md:text-base text-gray-700 mt-1">{{ $submission->kdp_notes ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Reward -->
            <div>
                <h3 class="text-sm md:text-base font-bold text-[#091E6E] border-l-4 border-[#091E6E] pl-3 mb-4">Reward</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div class="bg-gradient-to-r from-emerald-50 to-white p-4 rounded-xl border border-emerald-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nominal Reward</span>
                        <p class="text-2xl md:text-3xl font-bold text-emerald-600 mt-1">{{ $submission->reward_amount ? 'Rp ' . number_format($submission->reward_amount, 0, ',', '.') : '-' }}</p>
                    </div>
                    @if($submission->paid_at)
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Bayar</span>
                        <p class="text-sm md:text-base text-gray-700 mt-1">{{ \Carbon\Carbon::parse($submission->paid_at)->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
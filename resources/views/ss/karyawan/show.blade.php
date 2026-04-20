@extends('welcome')

@section('title', 'Detail SS')

@section('content')
<div class="animate-reveal pb-20 max-w-4xl mx-auto">
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
        <nav class="flex text-xs md:text-sm text-gray-400">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center text-gray-400">SS</li>
                <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
                <li class="text-[#091E6E] font-semibold tracking-tight uppercase text-[10px] md:text-xs">Detail SS</li>
            </ol>
        </nav>
        <a href="{{ route('ss.karyawan.index') }}" class="text-gray-500 hover:text-[#091E6E] text-sm"><i class="fa-regular fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="glass-card rounded-2xl p-6 md:p-8 shadow-sm border border-white space-y-6">
        <!-- Informasi Pengajuan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><span class="text-xs text-gray-400 uppercase">Tanggal Upload</span><p class="font-semibold">{{ \Carbon\Carbon::parse($submission->submission_date)->format('d/m/Y H:i') }}</p></div>
            <div><span class="text-xs text-gray-400 uppercase">Departemen</span><p class="font-semibold">{{ $submission->department_code ?? '-' }}</p></div>
            <div><span class="text-xs text-gray-400 uppercase">File PDF</span><p><a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="text-blue-600 hover:underline"><i class="fa-regular fa-file-pdf"></i> Buka PDF</a></p></div>
            <div><span class="text-xs text-gray-400 uppercase">Status</span>
                @php
                    $statusColors = ['draft'=>'gray','submitted'=>'yellow','assessed'=>'blue','spv_review'=>'purple','kdp_review'=>'orange','approved'=>'green','rejected'=>'red','rewarded'=>'emerald'];
                    $color = $statusColors[$submission->status] ?? 'gray';
                @endphp
                <span class="inline-block bg-{{ $color }}-100 text-{{ $color }}-800 px-2 py-1 rounded-full text-xs font-semibold uppercase">{{ str_replace('_', ' ', $submission->status) }}</span>
            </div>
        </div>

        <!-- Nilai & Catatan -->
        <div class="border-t pt-4">
            <h3 class="font-bold text-[#091E6E] mb-3">Penilaian</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><span class="text-xs text-gray-400 uppercase">Score</span><p class="text-2xl font-black {{ $submission->score ? 'text-blue-600' : 'text-gray-400' }}">{{ $submission->score ?? '-' }}</p></div>
                <div><span class="text-xs text-gray-400 uppercase">Catatan SS</span><p class="text-sm">{{ $submission->notes ?? '-' }}</p></div>
            </div>
        </div>

        <!-- Review SPV -->
        <div class="border-t pt-4">
            <h3 class="font-bold text-[#091E6E] mb-3">Review SPV</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><span class="text-xs text-gray-400 uppercase">Status</span><span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $submission->spv_status == 'approved' ? 'bg-green-100 text-green-800' : ($submission->spv_status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600') }}">{{ $submission->spv_status ?? 'pending' }}</span></div>
                <div><span class="text-xs text-gray-400 uppercase">Catatan SPV</span><p class="text-sm">{{ $submission->spv_notes ?? '-' }}</p></div>
            </div>
        </div>

        <!-- Review KDP -->
        <div class="border-t pt-4">
            <h3 class="font-bold text-[#091E6E] mb-3">Review KDP</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><span class="text-xs text-gray-400 uppercase">Status</span><span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $submission->kdp_status == 'approved' ? 'bg-green-100 text-green-800' : ($submission->kdp_status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600') }}">{{ $submission->kdp_status ?? 'pending' }}</span></div>
                <div><span class="text-xs text-gray-400 uppercase">Catatan KDP</span><p class="text-sm">{{ $submission->kdp_notes ?? '-' }}</p></div>
            </div>
        </div>

        <!-- Reward -->
        <div class="border-t pt-4">
            <h3 class="font-bold text-[#091E6E] mb-3">Reward</h3>
            <div><span class="text-xs text-gray-400 uppercase">Nominal Reward</span><p class="text-xl font-bold text-green-600">{{ $submission->reward_amount ? 'Rp ' . number_format($submission->reward_amount, 0, ',', '.') : '-' }}</p></div>
            @if($submission->paid_at)<div><span class="text-xs text-gray-400 uppercase">Tanggal Bayar</span><p>{{ \Carbon\Carbon::parse($submission->paid_at)->format('d/m/Y H:i') }}</p></div>@endif
        </div>
    </div>
</div>
@endsection
@extends('welcome')

@section('title', 'Detail SS - Admin')

@section('content')
<div class="animate-reveal pb-20 max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <nav class="text-xs text-gray-400"><a href="{{ route('ss.admin.submissions') }}" class="hover:text-[#091E6E]"><i class="fa-regular fa-arrow-left"></i> Kembali</a></nav>
        <div class="flex gap-2">
            @if(is_null($submission->score))
                <a href="{{ route('ss.admin.assess.form', $submission->id) }}" class="bg-blue-600 text-white px-4 py-1 rounded-xl text-sm">Beri Nilai</a>
            @endif
            @if($submission->status == 'assessed')
                <a href="{{ route('ss.admin.review_spv.form', $submission->id) }}" class="bg-purple-600 text-white px-4 py-1 rounded-xl text-sm">Review SPV</a>
            @endif
            @if($submission->status == 'kdp_review')
                <a href="{{ route('ss.admin.review_kdp.form', $submission->id) }}" class="bg-orange-600 text-white px-4 py-1 rounded-xl text-sm">Review KDP</a>
            @endif
            @if($submission->status == 'approved' && is_null($submission->reward_amount))
                <a href="{{ route('ss.admin.reward.form', $submission->id) }}" class="bg-emerald-600 text-white px-4 py-1 rounded-xl text-sm">Beri Reward</a>
            @endif
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-6">
        <!-- Sama seperti detail karyawan tetapi dengan tambahan tombol aksi -->
        <div class="grid grid-cols-2 gap-4">
            <div><span class="text-xs text-gray-400">Pengaju</span><p class="font-semibold">{{ $submission->employee->nama ?? $submission->employee_npk }}</p></div>
            <div><span class="text-xs text-gray-400">Departemen</span><p>{{ $submission->department_code }}</p></div>
            <div><span class="text-xs text-gray-400">Tanggal Upload</span><p>{{ \Carbon\Carbon::parse($submission->submission_date)->format('d/m/Y H:i') }}</p></div>
            <div><span class="text-xs text-gray-400">File PDF</span><p><a href="{{ asset('storage/'.$submission->file_path) }}" target="_blank" class="text-blue-600">Lihat PDF</a></p></div>
            <div><span class="text-xs text-gray-400">Score</span><p class="text-xl font-bold">{{ $submission->score ?? '-' }}</p></div>
            <div><span class="text-xs text-gray-400">Catatan SS</span><p>{{ $submission->notes ?? '-' }}</p></div>
        </div>
        <div class="border-t pt-4">
            <h4 class="font-bold">Review SPV</h4>
            <div class="grid grid-cols-2 gap-4 mt-2">
                <div><span class="text-xs text-gray-400">Status</span><p>{{ $submission->spv_status ?? 'pending' }}</p></div>
                <div><span class="text-xs text-gray-400">Catatan</span><p>{{ $submission->spv_notes ?? '-' }}</p></div>
            </div>
        </div>
        <div class="border-t pt-4">
            <h4 class="font-bold">Review KDP</h4>
            <div class="grid grid-cols-2 gap-4 mt-2">
                <div><span class="text-xs text-gray-400">Status</span><p>{{ $submission->kdp_status ?? 'pending' }}</p></div>
                <div><span class="text-xs text-gray-400">Catatan</span><p>{{ $submission->kdp_notes ?? '-' }}</p></div>
            </div>
        </div>
        <div class="border-t pt-4">
            <h4 class="font-bold">Reward</h4>
            <div><span class="text-xs text-gray-400">Nominal</span><p class="text-green-600 font-bold">{{ $submission->reward_amount ? 'Rp '.number_format($submission->reward_amount) : '-' }}</p></div>
            @if($submission->paid_at)<div><span class="text-xs text-gray-400">Tanggal Bayar</span><p>{{ \Carbon\Carbon::parse($submission->paid_at)->format('d/m/Y H:i') }}</p></div>@endif
        </div>
    </div>
</div>
@endsection
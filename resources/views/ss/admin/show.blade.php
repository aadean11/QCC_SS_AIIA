@extends('welcome')

@section('title', 'Detail SS - Admin')

@section('content')
<div class="animate-reveal pb-20">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Monitoring SS', 'icon' => 'fa-regular fa-lightbulb'],
        ['label' => 'Daftar Ide', 'url' => route('ss.admin.submissions')],
        'Detail Ide',
    ]])

        <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-white overflow-hidden">
            <!-- Header Card dengan Gradient -->
            <div class="sidebar-gradient px-4 md:px-8 py-4 md:py-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <div>
                    <h2 class="text-white text-lg md:text-2xl font-bold tracking-tight">Detail Suggestion System</h2>
                    <p class="text-blue-200 text-[10px] md:text-xs mt-1">Informasi lengkap pengajuan SS</p>
                </div>
                <div class="flex gap-2">
                    @if($submission->status == 'approved' && is_null($submission->paid_at))
                        <a href="{{ route('ss.admin.reward.form', $submission->id) }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 md:px-4 md:py-1.5 rounded-xl text-xs md:text-sm font-semibold transition shadow-md flex items-center gap-1">
                            <i class="fa-regular fa-money-bill-1"></i> Konfirmasi Reward
                        </a>
                    @endif
                </div>
            </div>

            <!-- Body Card -->
            <div class="p-4 md:p-8 space-y-6 md:space-y-8">

                <!-- Informasi Pengajuan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div class="bg-gray-50/50 p-3 md:p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Operator (OPR)</span>
                        <p class="text-sm md:text-base font-semibold text-gray-800 mt-1">{{ $submission->employee->nama ?? $submission->employee_npk }}</p>
                    </div>
                    <div class="bg-gray-50/50 p-3 md:p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Penilai</span>
                        <p class="text-sm md:text-base font-semibold text-gray-800 mt-1">{{ $submission->ldr->nama ?? $submission->ldr_npk ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50/50 p-3 md:p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Departemen</span>
                        <p class="text-sm md:text-base font-semibold text-gray-800 mt-1">{{ $submission->department_code }}</p>
                    </div>
                    <div class="bg-gray-50/50 p-3 md:p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Upload</span>
                        <p class="text-sm md:text-base font-semibold text-gray-800 mt-1">{{ \Carbon\Carbon::parse($submission->submission_date)->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="bg-gray-50/50 p-3 md:p-4 rounded-xl border border-gray-100">
                        <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">File PDF</span>
                        <p class="mt-1"><a href="{{ asset('storage/'.$submission->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-2 text-sm"><i class="fa-regular fa-file-pdf text-lg"></i> Lihat PDF</a></p>
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
                            <p class="text-2xl md:text-3xl font-bold {{ $submission->reward_amount ? 'text-emerald-600' : 'text-amber-600' }} mt-1">
                                {{ ($submission->reward_amount ?? $submission->calculated_reward_amount) ? 'Rp ' . number_format($submission->reward_amount ?? $submission->calculated_reward_amount, 0, ',', '.') : '-' }}
                            </p>
                            @if(!$submission->paid_at && $submission->calculated_reward_amount)
                                <span class="text-[10px] font-bold text-amber-600">Belum dibayar admin</span>
                            @endif
                        </div>
                        @if($submission->paid_at)
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                            <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Bayar</span>
                            <p class="text-sm md:text-base text-gray-700 mt-1">{{ \Carbon\Carbon::parse($submission->paid_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Tombol Aksi Bawah (opsional untuk navigasi) -->
                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <a href="{{ route('ss.admin.submissions') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-medium text-sm hover:bg-gray-50 transition-all duration-200 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
</div>
@endsection

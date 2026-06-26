@extends('welcome')

@section('title', 'Review Komite SS')

@section('content')
<div class="animate-reveal pb-20">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Monitoring SS', 'icon' => 'fa-regular fa-lightbulb'],
        ['label' => 'Review Komite', 'url' => route('ss.admin.review.index')],
        'Form Review',
    ]])

    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-white overflow-hidden">
        <div class="sidebar-gradient px-4 md:px-8 py-4 md:py-6">
            <h2 class="text-white text-lg md:text-2xl font-bold tracking-tight">Review Komite / Admin</h2>
            <p class="text-blue-200 text-[10px] md:text-xs mt-1">
                {{ $submission->employee->nama ?? $submission->employee_npk }}
                · Nilai SPV: <strong>{{ $submission->spv_score_total ?? '-' }}</strong>
                · Nilai KDP: <strong>{{ $submission->kdp_score_total ?? '-' }}</strong>
                · Nilai efektif: <strong>{{ $effectiveScore ?? '-' }}</strong>
            </p>
        </div>

        <div class="p-4 md:p-8">
            @if($submission->kdp_notes)
            <div class="mb-6 p-3 bg-orange-50 rounded-xl border border-orange-100">
                <span class="text-[10px] font-bold text-orange-600 uppercase">Catatan KDP</span>
                <p class="text-sm text-gray-700 mt-1">{{ $submission->kdp_notes }}</p>
            </div>
            @endif

            <form action="{{ route('ss.admin.review.store', $submission->id) }}" method="POST" id="formReviewAdmin">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">No Ide</span>
                        <p class="text-sm font-bold text-[#091E6E] mt-1">{{ $submission->idea_no ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Pengaju</span>
                        <p class="text-sm font-bold text-[#091E6E] mt-1">{{ $submission->employee->nama ?? $submission->employee_npk }}</p>
                    </div>
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Referensi Reward</span>
                        <p class="text-sm font-bold text-emerald-600 mt-1">Rp {{ number_format($referenceReward, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">File Ide</span>
                        <p class="mt-1"><a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="text-blue-600 hover:underline text-sm font-semibold"><i class="fa-regular fa-file-pdf mr-1"></i>Buka PDF</a></p>
                    </div>
                </div>

                @if($submission->spv_scores)
                    <div class="mb-6 overflow-x-auto border border-purple-100 rounded-xl">
                        <table class="w-full text-sm">
                            <thead class="bg-purple-50 text-purple-700">
                                <tr>
                                    <th class="text-left px-3 py-2">Kriteria SPV</th>
                                    <th class="text-center px-3 py-2 w-24">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criteria as $key => $label)
                                    <tr class="border-t border-purple-50">
                                        <td class="px-3 py-2 text-gray-700">{{ $loop->iteration }}. {{ $label }}</td>
                                        <td class="px-3 py-2 text-center font-bold text-[#091E6E]">{{ $submission->spv_scores[$key] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($submission->kdp_scores)
                    <div class="mb-6 overflow-x-auto border border-orange-100 rounded-xl">
                        <table class="w-full text-sm">
                            <thead class="bg-orange-50 text-orange-700">
                                <tr>
                                    <th class="text-left px-3 py-2">Kriteria KDP</th>
                                    <th class="text-center px-3 py-2 w-24">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criteria as $key => $label)
                                    <tr class="border-t border-orange-50">
                                        <td class="px-3 py-2 text-gray-700">{{ $loop->iteration }}. {{ $label }}</td>
                                        <td class="px-3 py-2 text-center font-bold text-[#091E6E]">{{ $submission->kdp_scores[$key] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if(!$requiresScoring)
                    <div class="mb-8 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <span class="text-[10px] font-bold text-blue-700 uppercase">Review komite tanpa penilaian ulang</span>
                        <p class="text-sm text-gray-700 mt-1">
                            Nilai efektif {{ $effectiveScore ?? 0 }} sudah sesuai batas approval SPV/KDP/Manager.
                            Komite cukup menyetujui, lalu reward dapat diberikan melalui menu Hasil &amp; Reward.
                        </p>
                    </div>
                @endif

                @if($requiresScoring)
                <div class="mb-8">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <label class="block text-gray-700 text-sm font-bold">Penilaian Final Komite <span class="text-red-500">*</span></label>
                        <span class="text-xs font-bold text-[#091E6E]">Total: <span id="adminScoreTotal">0</span></span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @php
                            $baseScores = $submission->kdp_scores ?? $submission->spv_scores ?? [];
                        @endphp
                        @foreach($criteria as $key => $label)
                            <div class="flex items-center justify-between gap-3 bg-gray-50/50 border border-gray-200 rounded-xl px-3 py-2">
                                <label class="text-xs md:text-sm font-semibold text-gray-700">
                                    {{ $loop->iteration }}. {{ $label }}
                                    <span class="block text-[9px] text-gray-400">Max {{ $criteriaMaxScores[$key] ?? 0 }}</span>
                                </label>
                                <input type="number" name="scores[{{ $key }}]" min="0" max="{{ $criteriaMaxScores[$key] ?? 0 }}" value="{{ old('scores.' . $key, $baseScores[$key] ?? 0) }}" class="score-input w-20 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-[#091E6E] text-center outline-none focus:ring-2 focus:ring-[#091E6E]">
                            </div>
                            @error('scores.' . $key) <p class="text-red-500 text-xs -mt-2">{{ $message }}</p> @enderror
                        @endforeach
                    </div>
                    @error('scores') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Admin / Komite</label>
                    <textarea name="admin_notes" rows="4" maxlength="20" class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] bg-gray-50/30 text-sm" placeholder="Catatan persetujuan komite (opsional)...">{{ old('admin_notes') }}</textarea>
                    @error('admin_notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('ss.admin.review.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-medium text-sm hover:bg-gray-50 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-[#091E6E] text-white rounded-xl font-bold text-sm hover:bg-[#130998] shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i> Approve &amp; Lanjut ke Reward
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateAdminScoreTotal() {
        const total = [...document.querySelectorAll('.score-input')].reduce((sum, input) => {
            const max = Number(input.max || 0);
            const value = Math.max(0, Math.min(Number(input.value || 0), max));
            input.value = value;
            return sum + value;
        }, 0);
        const totalEl = document.getElementById('adminScoreTotal');
        if (totalEl) totalEl.textContent = total;
    }

    document.querySelectorAll('.score-input').forEach(input => input.addEventListener('input', updateAdminScoreTotal));
    if (document.getElementById('adminScoreTotal')) updateAdminScoreTotal();

    document.getElementById('formReviewAdmin')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Approve SS ini?',
            text: 'SS akan disetujui komite. Reward dapat diberikan setelahnya melalui menu Hasil & Reward.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#091E6E',
            confirmButtonText: 'Ya, Approve',
            cancelButtonText: 'Batal'
        }).then((r) => { if (r.isConfirmed) this.submit(); });
    });
</script>
@endpush

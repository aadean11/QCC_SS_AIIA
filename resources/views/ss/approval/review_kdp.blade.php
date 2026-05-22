@extends('welcome')

@section('title', 'Review KDP')

@section('content')
<div class="animate-reveal pb-20">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Monitoring SS', 'icon' => 'fa-regular fa-lightbulb'],
        ['label' => 'Review SS (KDP)', 'url' => route('ss.approval.kdp')],
        'Form Review',
    ]])

    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-white overflow-hidden">
        <div class="sidebar-gradient px-4 md:px-8 py-4 md:py-6">
            <h2 class="text-white text-lg md:text-2xl font-bold tracking-tight">Review KDP</h2>
            <p class="text-blue-200 text-[10px] md:text-xs mt-1">
                {{ $submission->employee->nama ?? $submission->employee_npk }}
                · Score: <strong>{{ $submission->score }}</strong>
            </p>
        </div>

        <div class="p-4 md:p-8">
            @if($submission->spv_notes)
            <div class="mb-6 p-3 bg-purple-50 rounded-xl border border-purple-100">
                <span class="text-[10px] font-bold text-purple-600 uppercase">Catatan SPV</span>
                <p class="text-sm text-gray-700 mt-1">{{ $submission->spv_notes }}</p>
            </div>
            @endif

            <form action="{{ route('ss.approval.kdp.store', $submission->id) }}" method="POST" id="formReviewKdp">
                @csrf

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tindakan <span class="text-red-500">*</span></label>
                    <div class="bg-gray-50/30 rounded-xl border border-gray-200 p-2">
                        <select name="action" class="w-full bg-transparent outline-none text-sm md:text-base font-medium text-gray-800 p-2" required>
                            <option value="approved">✅ Approve</option>
                            <option value="rejected">❌ Reject</option>
                        </select>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Catatan KDP (opsional)</label>
                    <textarea name="kdp_notes" rows="4" class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] bg-gray-50/30 text-sm" placeholder="Tulis alasan persetujuan atau penolakan..."></textarea>
                    @error('kdp_notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('ss.approval.kdp') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-medium text-sm hover:bg-gray-50 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-orange-600 text-white rounded-xl font-bold text-sm hover:bg-orange-700 shadow-md flex items-center gap-2">
                        <i class="fa-regular fa-paper-plane"></i> Kirim Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('formReviewKdp')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Kirim Review KDP?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal'
        }).then((r) => { if (r.isConfirmed) this.submit(); });
    });
</script>
@endpush

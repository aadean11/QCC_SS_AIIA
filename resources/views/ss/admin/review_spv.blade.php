@extends('welcome')

@section('title', 'Review SPV')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumb -->
    <nav class="flex mb-4 md:mb-6 text-xs md:text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">SS</li>
            <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight text-[10px] md:text-xs">Review SPV</li>
        </ol>
    </nav>

    <!-- <div class="max-w-2xl mx-auto"> -->
        <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-white overflow-hidden">
            <!-- Header Card dengan Gradient -->
            <div class="sidebar-gradient px-4 md:px-8 py-4 md:py-6">
                <h2 class="text-white text-lg md:text-2xl font-bold tracking-tight">Review SPV</h2>
                <p class="text-blue-200 text-[10px] md:text-xs mt-1">Berikan persetujuan awal untuk ide SS ini</p>
            </div>

            <!-- Body Card -->
            <div class="p-4 md:p-8">
                <form action="{{ route('ss.admin.review_spv.store', $submission->id) }}" method="POST">
                    @csrf
                    
                    <!-- Tindakan Field -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tindakan <span class="text-red-500">*</span></label>
                        <div class="bg-gray-50/30 rounded-xl border border-gray-200 p-2">
                            <select name="action" class="w-full bg-transparent outline-none text-sm md:text-base font-medium text-gray-800 p-2" required>
                                <option value="approved">✅ Approve</option>
                                <option value="rejected">❌ Reject</option>
                            </select>
                        </div>
                    </div>

                    <!-- Catatan SPV -->
                    <div class="mb-8">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Catatan SPV (opsional)</label>
                        <textarea name="spv_notes" rows="4" class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] focus:border-transparent bg-gray-50/30 text-sm" placeholder="Tulis alasan persetujuan atau penolakan..."></textarea>
                        @error('spv_notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Aksi Tombol -->
                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('ss.admin.show', $submission->id) }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-medium text-sm hover:bg-gray-50 transition-all duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-ban mr-1"></i> Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white rounded-xl font-bold text-sm hover:bg-purple-700 transition-all duration-200 shadow-md flex items-center gap-2">
                            <i class="fa-regular fa-paper-plane"></i> Kirim Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <!-- </div> -->
</div>
@endsection
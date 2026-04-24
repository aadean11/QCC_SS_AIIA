@extends('welcome')

@section('title', 'Beri Reward')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumb -->
    <nav class="flex mb-4 md:mb-6 text-xs md:text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">SS</li>
            <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight text-[10px] md:text-xs">Beri Reward</li>
        </ol>
    </nav>

    <div class="max-w-2xl mx-auto">
        <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-white overflow-hidden">
            <!-- Header Card dengan Gradient -->
            <div class="sidebar-gradient px-4 md:px-8 py-4 md:py-6">
                <h2 class="text-white text-lg md:text-2xl font-bold tracking-tight">Beri Reward</h2>
                <p class="text-blue-200 text-[10px] md:text-xs mt-1">Masukkan nominal reward untuk ide SS ini</p>
            </div>

            <!-- Body Card -->
            <div class="p-4 md:p-8">
                <form action="{{ route('ss.admin.reward.store', $submission->id) }}" method="POST">
                    @csrf
                    
                    <!-- Nominal Reward Field -->
                    <div class="mb-8">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nominal Reward (Rp) <span class="text-red-500">*</span></label>
                        <div class="bg-gray-50/30 rounded-xl border border-gray-200 p-3">
                            <input type="number" name="reward_amount" min="0" step="1000" required 
                                class="w-full bg-transparent outline-none text-sm md:text-base font-medium text-gray-800 placeholder-gray-400"
                                placeholder="Contoh: 50000">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-info"></i> Masukkan nominal dalam Rupiah (tanpa titik atau koma)</p>
                        @error('reward_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Aksi Tombol -->
                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('ss.admin.show', $submission->id) }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-medium text-sm hover:bg-gray-50 transition-all duration-200 flex items-center gap-2">
                            <i class="fa-regular fa-ban mr-1"></i> Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition-all duration-200 shadow-md flex items-center gap-2">
                            <i class="fa-regular fa-money-bill-1"></i> Bayar Reward
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('welcome')

@section('title', 'Upload SS Baru')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumb -->
    <nav class="flex mb-4 md:mb-6 text-xs md:text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">SS</li>
            <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight text-[10px] md:text-xs">Upload SS Baru</li>
        </ol>
    </nav>

    <div class="w-full">
        <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-white overflow-hidden">
            <!-- Header Card dengan Gradient -->
            <div class="sidebar-gradient px-4 md:px-8 py-4 md:py-6">
                <h2 class="text-white text-lg md:text-2xl font-bold tracking-tight">Upload Suggestion System Baru</h2>
                <p class="text-blue-200 text-[10px] md:text-xs mt-1">Isi form di bawah untuk mengajukan ide SS Anda</p>
            </div>

            <!-- Body Card - Full Width -->
            <div class="p-4 md:p-8">
                <form action="{{ route('ss.karyawan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- File Upload Area -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">File PDF Ide SS <span class="text-red-500">*</span></label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#091E6E] transition-all duration-300 bg-gray-50/30">
                            <input type="file" name="file" accept=".pdf" required 
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#091E6E] file:text-white hover:file:bg-[#130998] file:transition-all file:cursor-pointer">
                            <p class="text-xs text-gray-400 mt-3 flex items-center justify-center gap-1"><i class="fa-solid fa-circle-info"></i> Maksimal 5MB, format PDF</p>
                        </div>
                        @error('file') 
                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1"><i class="fa-regular fa-circle-exclamation"></i> {{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div class="mb-8">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (opsional)</label>
                        <textarea name="notes" rows="4" class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] focus:border-transparent bg-gray-50/30 text-sm" placeholder="Tulis deskripsi singkat ide SS..."></textarea>
                        @error('notes') 
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Aksi Tombol -->
                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('ss.karyawan.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-medium text-sm hover:bg-gray-50 transition-all duration-200">
                            <i class="fa-solid fa-ban mr-1"></i> Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-[#091E6E] text-white rounded-xl font-bold text-sm hover:bg-[#130998] transition-all duration-200 shadow-md flex items-center gap-2">
                            <i class="fa-regular fa-paper-plane"></i> Upload SS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
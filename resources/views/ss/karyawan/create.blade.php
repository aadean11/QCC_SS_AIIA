@extends('welcome')

@section('title', 'Upload SS Baru')

@section('content')
<div class="animate-reveal pb-20 max-w-2xl mx-auto">
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
        <nav class="flex text-xs md:text-sm text-gray-400">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center text-gray-400">SS</li>
                <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
                <li class="text-[#091E6E] font-semibold tracking-tight uppercase text-[10px] md:text-xs">Upload SS Baru</li>
            </ol>
        </nav>
    </div>

    <div class="glass-card rounded-2xl p-6 md:p-8 shadow-sm border border-white">
        <form action="{{ route('ss.karyawan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-5">
                <label class="block text-gray-700 text-sm font-bold mb-2">File PDF Ide SS <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#091E6E] transition">
                    <input type="file" name="file" accept=".pdf" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#091E6E] file:text-white hover:file:bg-[#130998]">
                    <p class="text-xs text-gray-400 mt-2">Maksimal 5MB, format PDF</p>
                </div>
                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (opsional)</label>
                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-[#091E6E] focus:border-transparent" placeholder="Tulis deskripsi singkat ide SS..."></textarea>
                @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('ss.karyawan.index') }}" class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="px-5 py-2 bg-[#091E6E] text-white rounded-xl hover:bg-[#130998] transition shadow-md">Upload SS</button>
            </div>
        </form>
    </div>
</div>
@endsection
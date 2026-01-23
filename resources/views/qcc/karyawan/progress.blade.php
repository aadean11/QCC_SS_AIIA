@extends('welcome')
@section('title', 'Update Progress')
@section('content')
<div class="animate-reveal">
    <h2 class="text-3xl font-bold text-[#091E6E] mb-2">Pusat Progress QCC</h2>
    <p class="text-gray-400 text-sm mb-8 italic">Silakan unduh template dan unggah progres sesuai langkah PDCA.</p>

    <!-- CONTAINER TEMPLATE (FOLDER STYLE) -->
    <div class="glass-card rounded-[2.5rem] p-8 border border-white shadow-sm mb-10">
        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
            <i class="fa-solid fa-folder-open text-amber-500"></i> Download Template Progres (PPT/Excel)
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
            @foreach($steps as $s)
            <a href="#" class="flex flex-col items-center justify-center p-4 bg-white rounded-3xl border border-gray-100 hover:shadow-md hover:scale-105 transition-all group">
                <i class="fa-solid fa-file-powerpoint text-3xl text-red-500 mb-2 group-hover:animate-bounce"></i>
                <span class="text-[10px] font-black text-[#091E6E]">STEP {{ $s->step_number }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <!-- AREA UPLOAD PER STEP -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @foreach($steps as $step)
        @php $up = $uploads[$step->id] ?? null; @endphp
        <div class="glass-card p-6 rounded-[2.5rem] border border-white shadow-sm relative overflow-hidden group">
            <div class="flex justify-between items-start mb-6">
                <div class="w-12 h-12 sidebar-gradient rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg">
                    {{ $step->step_number }}
                </div>
                @if($up)
                    <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase {{ $up->status == 'APPROVED' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                        {{ $up->status }}
                    </span>
                @endif
            </div>

            <h4 class="text-[#091E6E] font-bold text-sm uppercase tracking-tight">{{ $step->step_name }}</h4>
            <p class="text-[10px] text-gray-400 mt-2 line-clamp-2">{{ $step->description }}</p>

            <div class="mt-8 pt-4 border-t border-gray-50">
                @if($up)
                    <div class="flex items-center gap-3 mb-4 p-2 bg-blue-50 rounded-xl">
                        <i class="fa-solid fa-circle-check text-blue-500 text-sm"></i>
                        <span class="text-[10px] font-bold text-blue-800 truncate">{{ $up->file_name }}</span>
                    </div>
                @endif
                <button class="w-full py-3 {{ $up ? 'bg-gray-100 text-gray-500' : 'bg-[#091E6E] text-white shadow-blue-200' }} rounded-xl text-[10px] font-bold uppercase tracking-widest hover:scale-105 transition-all">
                    {{ $up ? 'Ganti File' : 'Upload File' }}
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
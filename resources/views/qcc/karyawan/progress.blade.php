@extends('welcome')
@section('title', 'Update Progress')
@section('content')
<div class="animate-reveal">
    <!-- HEADER & THEME SWITCHER -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-8 gap-6">
        <div class="flex-1">
            <h2 class="text-3xl font-bold text-[#091E6E]">Pusat Progress QCC</h2>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-lg font-bold uppercase tracking-tighter">
                    Circle: {{ $theme->circle->circle_name }}
                </span>
                <span class="text-xs bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-lg font-bold uppercase tracking-tighter">
                    {{ $theme->status }}
                </span>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4 w-full xl:w-auto">
            <!-- DROPDOWN PILIH TEMA -->
            <div class="flex items-center gap-3 bg-white p-2 px-4 rounded-2xl shadow-sm border border-gray-100 transition-all hover:border-[#091E6E]">
                <i class="fa-solid fa-lightbulb text-amber-400 text-sm"></i>
                <div class="flex flex-col">
                    <span class="text-[9px] font-bold text-gray-400 uppercase leading-none">Pilih Tema:</span>
                    <select onchange="window.location.href='/qcc/karyawan/progress?theme_id=' + this.value" 
                        class="text-sm font-bold text-[#091E6E] outline-none bg-transparent cursor-pointer min-w-[200px] max-w-[300px]">
                        @foreach($myThemes as $mt)
                            <option value="{{ $mt->id }}" {{ $theme->id == $mt->id ? 'selected' : '' }}>
                                {{ $mt->theme_name }} ({{ $mt->circle->circle_name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <a href="{{ route('qcc.karyawan.themes', ['circle_id' => $theme->qcc_circle_id]) }}" class="bg-gray-100 text-gray-500 px-6 py-3 rounded-2xl font-bold hover:bg-gray-200 transition-all text-xs flex items-center justify-center gap-2 shadow-sm uppercase tracking-widest active:scale-95">
                <i class="fa-solid fa-list"></i> Daftar Tema
            </a>
        </div>
    </div>

    <!-- CONTAINER TEMPLATE (STEP 1-8) -->
    <div class="glass-card rounded-[2.5rem] p-8 border border-white shadow-sm mb-10">
        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
            <i class="fa-solid fa-folder-open text-amber-500"></i> Download Template Progres (Resmi)
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
            @foreach($actualSteps as $s)
                @if($s->template_file_path)
                    <a href="{{ asset('storage/' . $s->template_file_path) }}" target="_blank" class="flex flex-col items-center justify-center p-4 bg-white rounded-3xl border border-gray-100 hover:shadow-md hover:scale-105 transition-all group">
                        <i class="fa-solid fa-file-powerpoint text-3xl text-red-500 mb-2 group-hover:animate-bounce"></i>
                        <span class="text-[10px] font-black text-[#091E6E] uppercase text-center">STEP {{ $s->step_number }}</span>
                    </a>
                @else
                    <div class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-3xl border border-dashed border-gray-200 opacity-50 cursor-not-allowed">
                        <i class="fa-solid fa-file-circle-xmark text-3xl text-gray-300 mb-2"></i>
                        <span class="text-[10px] font-black text-gray-400 uppercase">STEP {{ $s->step_number }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- AREA UPLOAD PER STEP -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @foreach($actualSteps as $index => $step)
            @php 
                $up = $uploads[$step->id] ?? null; 
                $isLocked = $step->is_locked; 
            @endphp

            <div class="glass-card p-6 rounded-[2.5rem] border border-white shadow-sm relative overflow-hidden transition-all duration-500 
                {{ $isLocked ? 'opacity-60 grayscale' : 'hover:shadow-xl hover:-translate-y-1' }}">
                
                @if($isLocked)
                    <div class="absolute inset-0 z-10 flex items-center justify-center bg-gray-900/5 backdrop-blur-[1px]">
                        <div class="bg-white/80 p-3 rounded-full shadow-lg border border-gray-100">
                            <i class="fa-solid fa-lock text-[#091E6E] text-xl"></i>
                        </div>
                    </div>
                @endif

                <div class="flex justify-between items-start mb-6">
                    <div class="w-12 h-12 {{ $up && $up->status == 'APPROVED' ? 'bg-emerald-500' : 'sidebar-gradient' }} rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg">
                        @if($up && $up->status == 'APPROVED')
                            <i class="fa-solid fa-check text-lg"></i>
                        @else
                            {{ $step->step_number }}
                        @endif
                    </div>
                    
                    @if($up)
                        @php
                            $color = 'bg-gray-100 text-gray-600 border-gray-200';
                            if($up->status == 'APPROVED') { $color = 'bg-emerald-100 text-emerald-600 border-emerald-200'; } 
                            elseif($up->status == 'WAITING SPV') { $color = 'bg-amber-100 text-amber-600 border-amber-200'; } 
                            elseif($up->status == 'WAITING KDP') { $color = 'bg-blue-100 text-blue-600 border-blue-200'; } 
                            elseif(str_contains($up->status, 'REJECTED')) { $color = 'bg-red-100 text-red-600 border-red-200'; }
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase border {{ $color }}">
                            {{ $theme->status !== 'ACTIVE' ? 'HISTORICAL: ' : '' }}{{ $up->status }}
                        </span>
                    @elseif(!$isLocked)
                        <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase bg-blue-50 text-blue-500 border border-blue-100 animate-pulse">
                            Ready
                        </span>
                    @endif
                </div>

                <h4 class="text-[#091E6E] font-bold text-sm uppercase tracking-tight">{{ $step->step_name }}</h4>
                <p class="text-[10px] text-gray-400 mt-2 line-clamp-2 leading-relaxed">{{ $step->description }}</p>

                @if($up && str_contains($up->status, 'REJECTED'))
                    <div class="mt-3 p-2 bg-red-50 rounded-lg border border-red-100 text-left">
                        <p class="text-[9px] font-bold text-red-600 uppercase leading-none">Alasan Penolakan:</p>
                        <p class="text-[10px] text-red-500 italic mt-1 leading-tight">"{{ $up->kdp_note ?? $up->spv_note }}"</p>
                    </div>
                @endif

                <div class="mt-8 pt-4 border-t border-gray-50 text-left">
                    @if($up)
                        <div class="flex items-center gap-3 mb-4 p-2 bg-blue-50/50 rounded-xl border border-blue-100">
                            <i class="fa-solid fa-file-lines text-[#091E6E]"></i>
                            <span class="text-[10px] font-bold text-[#091E6E] truncate">{{ $up->file_name }}</span>
                        </div>
                    @endif

                    @if(!$isLocked)
                        @if($up && $up->status == 'APPROVED')
                            <div class="w-full py-3 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-bold uppercase text-center border border-emerald-100 italic">
                                <i class="fa-solid fa-circle-check mr-1"></i> Selesai
                            </div>
                        @else
                            <button onclick="openUploadModal({{ $step->id }}, '{{ $step->step_name }}')" 
                                class="w-full py-3 {{ $up ? 'bg-amber-500' : 'bg-[#091E6E]' }} text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg hover:brightness-110 transition-all active:scale-95">
                                <i class="fa-solid {{ $up ? 'fa-arrows-rotate' : 'fa-cloud-arrow-up' }} mr-2"></i>
                                {{ $up ? 'Update Progres' : 'Upload Progres' }}
                            </button>
                        @endif
                    @else
                        <button disabled class="w-full py-3 bg-gray-200 text-gray-400 rounded-xl text-[10px] font-bold uppercase cursor-not-allowed">
                            Step {{ $step->step_number }} Terkunci
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- ================= MODAL UPLOAD ================= -->
<div id="modalUpload" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-left">
        <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl animate-reveal overflow-hidden border border-white">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold uppercase tracking-widest text-white">Upload Progres</h3>
                    <p id="modalStepName" class="text-[10px] text-blue-200 font-medium uppercase mt-1"></p>
                </div>
                <button onclick="closeModal('modalUpload')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            
            <form action="{{ route('qcc.karyawan.upload_file') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="qcc_step_id" id="upload_step_id">
                <input type="hidden" name="qcc_theme_id" value="{{ $theme->id }}">
                <input type="hidden" name="qcc_circle_id" value="{{ $theme->qcc_circle_id }}">

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 text-left block">Pilih File (PDF/PPTX)</label>
                    <div class="relative group">
                        <input type="file" name="file" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                        <div class="w-full p-8 border-2 border-dashed border-gray-200 rounded-3xl group-hover:border-[#091E6E] group-hover:bg-blue-50 transition-all text-center">
                            <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-300 group-hover:text-[#091E6E] mb-3"></i>
                            <p id="fileNameDisplay" class="text-xs text-gray-400 font-medium text-center">Klik atau drop file di sini</p>
                            <p class="text-[9px] text-gray-300 mt-1 uppercase text-center">Maksimal 10MB</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-[#091E6E] text-white rounded-2xl font-bold shadow-lg hover:bg-[#130998] transition-all uppercase tracking-widest text-xs">
                    Kirim Progres Langkah
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) { 
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden'); 
            document.body.style.overflow = 'hidden'; 
        }
    }
    
    function closeModal(id) { 
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden'); 
            document.body.style.overflow = 'auto'; 
        }
    }

    function openUploadModal(stepId, stepName, isUpdate) {
        document.getElementById('upload_step_id').value = stepId;
        document.getElementById('modalStepName').innerText = "LANGKAH: " + stepName;
        document.getElementById('fileNameDisplay').innerText = "Klik atau drop file di sini";
        openModal('modalUpload');
    }

    function updateFileName(input) {
        const display = document.getElementById('fileNameDisplay');
        if (input.files.length > 0) {
            display.innerText = input.files[0].name;
            display.classList.add('text-[#091E6E]', 'font-bold');
        }
    }

    // SweetAlert handling
    @if(Session::has('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ Session::get('success') }}", timer: 2500, showConfirmButton: false });
    @endif
    
    @if(Session::has('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ Session::get('error') }}", confirmButtonColor: '#091E6E' });
    @endif
</script>
@endpush
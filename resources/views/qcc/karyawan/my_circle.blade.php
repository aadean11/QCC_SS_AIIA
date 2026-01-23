@extends('welcome')
@section('title', 'Circle Saya')
@section('content')
<div class="animate-reveal">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-[#091E6E]">Circle QCC Saya</h2>
        @if(!$circle)
            <button onclick="openModal('modalCreateCircle')" class="bg-[#091E6E] text-white px-6 py-3 rounded-2xl font-bold shadow-lg hover:bg-[#130998] transition-all">
                <i class="fa-solid fa-plus mr-2"></i> BUAT CIRCLE BARU
            </button>
        @endif
    </div>

    @if($circle)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- INFO CIRCLE -->
            <div class="lg:col-span-2 glass-card rounded-[2.5rem] p-10 border border-white shadow-sm">
                <div class="flex items-center gap-6 mb-10">
                    <div class="w-20 h-20 sidebar-gradient rounded-[1.5rem] flex items-center justify-center text-white text-3xl shadow-xl">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-black text-[#091E6E]">{{ $circle->circle_name }}</h3>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs mt-1">{{ $circle->circle_code }} | DEPT: {{ $user->department_code }}</p>
                    </div>
                </div>

                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Daftar Anggota</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($circle->members as $m)
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-[#091E6E] font-black shadow-sm">
                            {{ substr($m->employee->nama, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#091E6E]">{{ $m->employee->nama }}</p>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full {{ $m->role == 'LEADER' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600' }}">
                                {{ $m->role }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- TEMA SAAT INI -->
            <div class="glass-card rounded-[2.5rem] p-8 border border-white bg-blue-50/30">
                <h4 class="text-[10px] font-bold text-[#091E6E] uppercase tracking-[0.2em] mb-6">Tema Aktif</h4>
                @php $activeTheme = $circle->themes->where('status', 'ACTIVE')->first(); @endphp
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-blue-100">
                    <i class="fa-solid fa-lightbulb text-amber-400 text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold text-[#091E6E] leading-tight mb-4">{{ $activeTheme->theme_name }}</h3>
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Periode: {{ $activeTheme->period->period_name }}</p>
                </div>
            </div>
        </div>
    @else
        <!-- EMPTY STATE -->
        <div class="glass-card rounded-[3rem] p-20 text-center border-2 border-dashed border-gray-200">
            <img src="{{ asset('assets/images/empty-circle.png') }}" class="w-48 mx-auto opacity-20 mb-6">
            <h3 class="text-xl font-bold text-gray-400">Anda belum tergabung dalam Circle QCC</h3>
            <p class="text-gray-400 text-sm mt-2">Silakan buat kelompok baru bersama rekan satu departemen Anda.</p>
        </div>
    @endif
</div>

<!-- MODAL CREATE CIRCLE -->
<div id="modalCreateCircle" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-[2.5rem] w-full max-w-3xl shadow-2xl animate-reveal overflow-hidden">
            <div class="sidebar-gradient p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold uppercase tracking-widest">Buat Circle QCC Baru</h3>
                <button onclick="closeModal('modalCreateCircle')" class="text-white/70 hover:text-white text-2xl">&times;</button>
            </div>
            <form action="{{ route('qcc.karyawan.store_circle') }}" method="POST" class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Kelompok (Circle)</label>
                        <input type="text" name="circle_name" required placeholder="Masukkan nama unik..." class="w-full mt-2 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#091E6E] outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Pilih Periode</label>
                        <select name="qcc_period_id" required class="w-full mt-2 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#091E6E] outline-none font-bold">
                            @foreach($activePeriods as $p) <option value="{{ $p->id }}">{{ $p->period_name }} ({{ $p->year }})</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tema Pertama</label>
                        <input type="text" name="theme_name" required placeholder="Apa yang ingin diperbaiki?" class="w-full mt-2 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#091E6E] outline-none">
                    </div>
                </div>
                <div class="space-y-4">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 block">Pilih Anggota (Dept: {{ $user->department_code }})</label>
                    <div class="h-64 overflow-y-auto bg-gray-50 p-4 rounded-3xl border border-gray-200 space-y-2">
                        @foreach($colleagues as $col)
                        <label class="flex items-center gap-3 p-3 bg-white rounded-xl cursor-pointer border border-transparent hover:border-blue-300">
                            <input type="checkbox" name="members[]" value="{{ $col->npk }}" class="rounded text-[#091E6E]">
                            <span class="text-xs font-bold text-gray-700">{{ $col->nama }} <span class="text-gray-300 font-normal">({{ $col->npk }})</span></span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="md:col-span-2 py-4 bg-[#091E6E] text-white rounded-2xl font-bold shadow-lg hover:bg-[#130998] transition-all uppercase tracking-widest text-xs">Konfirmasi & Daftarkan Circle</button>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('welcome')

@section('title', 'Beranda - SIGITA')

@section('content')
<div class="animate-reveal space-y-6 md:space-y-10">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 md:gap-6">
        <div>
            <nav class="flex mb-2 text-xs uppercase tracking-widest text-gray-400 font-bold">
                <ol class="inline-flex items-center space-x-2">
                    <li>Home</li>
                    <li><i class="fa-solid fa-chevron-right text-[8px]"></i></li>
                    <li class="text-[#091E6E]">Dashboard Overview</li>
                </ol>
            </nav>

            <h2 class="text-2xl md:text-3xl font-black text-[#091E6E]">
                Statistik Performa Utama
            </h2>

            <p class="text-gray-500 text-xs md:text-sm italic mt-1">
                Halo {{ $user->nama }}, berikut adalah ringkasan aktivitas perbaikan saat ini.
            </p>
        </div>

        <!-- VIEW SCOPE -->
        <div class="glass-card px-4 md:px-6 py-3 md:py-4 rounded-2xl border-l-4 border-blue-600 shadow-sm flex items-center gap-3 md:gap-4 w-full md:w-auto">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                <i class="fa-solid fa-layer-group text-sm md:text-base"></i>
            </div>
            <div>
                <p class="text-[8px] md:text-[9px] font-bold text-gray-400 uppercase tracking-wider">
                    Cakupan Data (Scope)
                </p>
                <p class="text-[10px] md:text-xs font-black text-[#091E6E] uppercase">
                    {{ $viewScope }}
                </p>
            </div>
        </div>
    </div>

    <!-- MAIN STATS GRID -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 md:gap-8">

        <!-- ================= QCC CARD ================= -->
        <div class="glass-card rounded-[2rem] md:rounded-[2.5rem] border border-white shadow-sm hover:shadow-2xl transition-all duration-500 group overflow-hidden bg-gradient-to-br from-white to-blue-50/30">

            <div class="p-6 md:p-8 relative">
                <div class="flex justify-between items-start mb-6 md:mb-8">
                    <div>
                        <p class="text-blue-500 font-black uppercase text-[8px] md:text-[10px] tracking-widest mb-1">
                            Activity Analysis
                        </p>
                        <h3 class="text-xl md:text-2xl font-bold text-[#091E6E]">
                            Quality Control Circle
                        </h3>
                    </div>

                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-white shadow-lg flex items-center justify-center text-2xl md:text-3xl text-[#091E6E] group-hover:scale-110 transition-transform duration-500">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                </div>

                <div class="flex items-end gap-3 md:gap-4">
                    <div class="text-6xl md:text-8xl font-black text-[#091E6E] tracking-tighter leading-none">
                        {{ $jumlahQcc }}
                    </div>
                    <div class="pb-2 md:pb-3">
                        <p class="text-gray-400 font-bold uppercase text-[10px] md:text-xs tracking-wider">
                            Total Circle
                        </p>
                        <p class="text-emerald-500 font-bold text-[8px] md:text-[10px] flex items-center gap-1 uppercase">
                            <i class="fa-solid fa-chart-line"></i> Terdaftar Aktif
                        </p>
                    </div>
                </div>

                <!-- Decorative Icon -->
                <i class="fa-solid fa-users-gear absolute -right-6 -bottom-10 text-[8rem] md:text-[12rem] text-blue-900/5 pointer-events-none"></i>
            </div>

            <!-- Bottom Bar -->
            <div class="bg-gradient-to-r from-[#091E6E] to-blue-800 py-3 md:py-4 rounded-b-[2rem] md:rounded-b-[2.5rem] border-t border-blue-700/30">
                <p class="text-blue-200 text-[8px] md:text-[10px] font-bold uppercase tracking-[0.2em] text-center">
                    Monitoring Progres PDCA 8-Steps
                </p>
            </div>

        </div>

        <!-- ================= SS CARD ================= -->
        <div class="glass-card rounded-[2rem] md:rounded-[2.5rem] border border-white shadow-sm hover:shadow-2xl transition-all duration-500 group overflow-hidden bg-gradient-to-br from-white to-amber-50/30">

            <div class="p-6 md:p-8 relative">
                <div class="flex justify-between items-start mb-6 md:mb-8">
                    <div>
                        <p class="text-amber-500 font-black uppercase text-[8px] md:text-[10px] tracking-widest mb-1">
                            Idea Generation
                        </p>
                        <h3 class="text-xl md:text-2xl font-bold text-[#091E6E]">
                            Suggestion System
                        </h3>
                    </div>

                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-white shadow-lg flex items-center justify-center text-2xl md:text-3xl text-amber-500 group-hover:scale-110 transition-transform duration-500">
                        <i class="fa-regular fa-lightbulb"></i>
                    </div>
                </div>

                <div class="flex items-end gap-3 md:gap-4">
                    <div class="text-6xl md:text-8xl font-black text-[#091E6E] tracking-tighter leading-none">
                        {{ $jumlahSs }}
                    </div>
                    <div class="pb-2 md:pb-3">
                        <p class="text-gray-400 font-bold uppercase text-[10px] md:text-xs tracking-wider">
                            Total Saran
                        </p>
                        <p class="text-amber-600 font-bold text-[8px] md:text-[10px] flex items-center gap-1 uppercase">
                            <i class="fa-solid fa-bolt"></i> Inovasi Individu
                        </p>
                    </div>
                </div>

                <!-- Decorative Icon -->
                <i class="fa-regular fa-lightbulb absolute -right-6 -bottom-10 text-[8rem] md:text-[12rem] text-amber-900/5 pointer-events-none"></i>
            </div>

            <!-- Bottom Bar -->
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 py-3 md:py-4 rounded-b-[2rem] md:rounded-b-[2.5rem] border-t border-amber-400/30">
                <p class="text-amber-50 text-[8px] md:text-[10px] font-bold uppercase tracking-[0.2em] text-center">
                    Pusat Pengumpulan Gagasan Kreatif Karyawan
                </p>
            </div>

        </div>

    </div>

    <!-- LOWER INFO SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">

        <div class="lg:col-span-2 glass-card p-4 md:p-6 rounded-2xl md:rounded-3xl border border-gray-100 flex flex-col sm:flex-row items-start sm:items-center gap-4 shadow-sm">
            <div class="w-8 h-8 md:w-12 md:h-12 bg-[#091E6E] text-white rounded-full flex items-center justify-center shrink-0">
                <i class="fa-solid fa-info text-sm md:text-base"></i>
            </div>
            <p class="text-[10px] md:text-xs text-gray-500 leading-relaxed">
                Data di atas diperbarui secara otomatis berdasarkan sistem <strong>SIGITA</strong>. 
                Angka mencerminkan jumlah pendaftaran yang sudah diverifikasi dan masuk ke dalam periode aktif saat ini sesuai dengan otoritas 
                <span class="text-[#091E6E] font-bold uppercase">{{ $user->occupation }}</span>.
            </p>
        </div>

        <div class="glass-card p-4 md:p-6 rounded-2xl md:rounded-3xl border border-gray-100 flex items-center justify-center text-center shadow-sm">
            <div>
                <p class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                    Tahun Periode
                </p>
                <p class="text-lg md:text-xl font-black text-[#091E6E]">
                    {{ date('Y') }}
                </p>
            </div>
        </div>

    </div>

</div>
@endsection
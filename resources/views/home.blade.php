@extends('welcome')

@section('title', 'Beranda - Satu AISIN')

@section('content')
    <div class="animate-reveal">
        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-sm">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-gray-400">
                <li class="inline-flex items-center"><i class="fa-solid fa-house mr-2 text-xs"></i> Home</li>
                <li><div class="flex items-center font-semibold text-[#091E6E]"><i class="fa-solid fa-chevron-right text-[10px] mx-2 text-gray-400"></i> Dashboard Overview</div></li>
            </ol>
        </nav>

        <!-- Welcome Banner -->
        <div class="welcome-banner rounded-[2rem] p-8 md:p-12 mb-10 shadow-xl relative overflow-hidden">
            <div class="relative z-10 max-w-2xl text-white">
                <h4 class="text-blue-200 font-medium mb-2 uppercase tracking-widest text-sm text-white">Overview System</h4>
                <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4">Halo, <span class="text-yellow-400">{{ $user->nama }}!</span></h1>
                <p class="text-blue-100 text-lg opacity-90">Pantau kemajuan ide dan perubahan melalui sistem Monitoring QCC dan SS secara real-time.</p>
            </div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <div class="glass-card rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all relative overflow-hidden">
                <div>
                    <p class="text-gray-400 font-medium uppercase text-[10px] mb-1">Total Submission</p>
                    <h3 class="text-xl font-bold text-[#091E6E] mb-6">Quality Control Circle</h3>
                    <div class="text-7xl font-extrabold text-[#091E6E]">{{ $jumlahQcc }}</div>
                </div>
                <div class="absolute top-8 right-8 w-14 h-14 rounded-2xl 
                            bg-gradient-to-br from-emerald-400 to-green-600
                            flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-users-gear text-xl text-white"></i>
                </div>
                <i class="fa-solid fa-users-gear absolute -right-4 -bottom-4 text-9xl text-gray-400 opacity-50 -z-0"></i>
            </div>

            <div class="glass-card rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all relative overflow-hidden">
                <div>
                    <p class="text-gray-400 font-medium uppercase text-[10px] mb-1">Total Submission</p>
                    <h3 class="text-xl font-bold text-[#091E6E] mb-6">Suggestion System</h3>
                    <div class="text-7xl font-extrabold text-[#091E6E]">{{ $jumlahSs }}</div>
                </div>
                <div class="absolute top-8 right-8 w-14 h-14 rounded-2xl 
                            bg-gradient-to-br from-yellow-300 to-amber-500
                            flex items-center justify-center shadow-lg">
                    <i class="fa-regular fa-lightbulb text-xl text-white"></i>
                </div>
                <i class="fa-regular fa-lightbulb absolute -right-4 -bottom-4 text-9xl text-gray-400 opacity-50 -z-0"></i>
            </div>
        </div>
    </div>
@endsection
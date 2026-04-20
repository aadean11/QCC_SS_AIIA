@extends('welcome')

@section('title', 'Dashboard SS')

@section('content')
<div class="animate-reveal pb-20">
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
        <nav class="flex text-xs md:text-sm text-gray-400">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center text-gray-400">SS</li>
                <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
                <li class="text-[#091E6E] font-semibold tracking-tight uppercase text-[10px] md:text-xs">Dashboard</li>
            </ol>
        </nav>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="glass-card p-5 rounded-2xl border-l-4 border-blue-600">
            <div class="flex justify-between"><div><p class="text-xs text-gray-500 uppercase">Total SS</p><h3 class="text-2xl font-black text-[#091E6E]">{{ $total }}</h3></div><i class="fa-regular fa-lightbulb text-3xl text-blue-200"></i></div>
        </div>
        <div class="glass-card p-5 rounded-2xl border-l-4 border-yellow-500">
            <div class="flex justify-between"><div><p class="text-xs text-gray-500 uppercase">Belum Dinilai</p><h3 class="text-2xl font-black text-yellow-600">{{ $pendingScore }}</h3></div><i class="fa-regular fa-hourglass-half text-3xl text-yellow-200"></i></div>
        </div>
        <div class="glass-card p-5 rounded-2xl border-l-4 border-green-500">
            <div class="flex justify-between"><div><p class="text-xs text-gray-500 uppercase">Approved</p><h3 class="text-2xl font-black text-green-600">{{ $approved }}</h3></div><i class="fa-regular fa-circle-check text-3xl text-green-200"></i></div>
        </div>
        <div class="glass-card p-5 rounded-2xl border-l-4 border-emerald-500">
            <div class="flex justify-between"><div><p class="text-xs text-gray-500 uppercase">Sudah Reward</p><h3 class="text-2xl font-black text-emerald-600">{{ $rewarded }}</h3></div><i class="fa-regular fa-money-bill-1 text-3xl text-emerald-200"></i></div>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 text-center text-gray-400">
        <i class="fa-solid fa-chart-simple text-4xl mb-2"></i>
        <p>Grafik perkembangan SS akan ditampilkan di sini (Coming Soon).</p>
    </div>
</div>
@endsection
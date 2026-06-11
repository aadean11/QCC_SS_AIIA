@extends('welcome')

@section('title', 'Beranda - SIGITA')

@section('content')
@php
    $lastUpdateText = $lastUpdateAt ? $lastUpdateAt->format('d M Y, H:i') : '-';
    $latestQccText = $latestQccAt ? $latestQccAt->format('d M Y') : '-';
    $latestSsText = $latestSsAt ? $latestSsAt->format('d M Y') : '-';

    $roleBadgeColor = match($activeRole) {
        'admin' => 'bg-blue-50 text-blue-700 border-blue-100',
        'employee' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        default => 'bg-slate-50 text-slate-700 border-slate-100',
    };

    $progressWidth = function ($value) {
        return max(0, min(100, (int) $value));
    };
@endphp

<div class="animate-reveal space-y-6 md:space-y-8">

    <!-- HERO -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] border border-white shadow-sm bg-gradient-to-br from-white via-white to-slate-50">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 -right-16 w-56 h-56 bg-blue-100/60 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-10 w-56 h-56 bg-amber-100/50 rounded-full blur-3xl"></div>
        </div>

        <div class="relative p-5 md:p-8">
            <div class="grid grid-cols-1 xl:grid-cols-[1fr_420px] gap-6 xl:gap-8 items-start">
                <!-- LEFT -->
                <div class="max-w-3xl">
                    <div class="mb-3 md:mb-4">
                        @include('partials.breadcrumb', ['items' => [
                            ['label' => 'Beranda', 'icon' => 'fa-solid fa-house'],
                            'Dashboard Overview',
                        ]])
                    </div>

                    <div class="space-y-3 md:space-y-4">
                        <div>
                            <p class="text-[10px] md:text-xs font-black uppercase tracking-[0.25em] text-[#091E6E]">
                                Dashboard Overview
                            </p>
                            <h2 class="mt-2 text-2xl md:text-4xl font-black text-[#091E6E] leading-tight">
                                Statistik Performa Utama
                            </h2>
                            <p class="mt-3 text-gray-500 text-xs md:text-sm leading-relaxed max-w-2xl">
                                Halo {{ $user->nama }}, berikut ringkasan performa operasional yang disesuaikan dengan hak akses Anda di SIGITA.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3 pt-1">
                            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 text-blue-700 px-3 py-2 text-[10px] md:text-xs font-bold border border-blue-100">
                                <i class="fa-solid fa-signal"></i>
                                Ringkasan operasional
                            </div>
                            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 text-emerald-700 px-3 py-2 text-[10px] md:text-xs font-bold border border-emerald-100">
                                <i class="fa-solid fa-shield-halved"></i>
                                {{ $roleLabel }}
                            </div>
                            <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 text-amber-700 px-3 py-2 text-[10px] md:text-xs font-bold border border-amber-100">
                                <i class="fa-solid fa-layer-group"></i>
                                {{ $scopeLevel }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="grid grid-cols-1 gap-3 w-full">
                    <div class="glass-card px-4 py-3 rounded-2xl border shadow-sm flex items-center gap-3 {{ $roleBadgeColor }}">
                        <div class="w-10 h-10 rounded-xl bg-white/80 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-bold uppercase tracking-wider opacity-70">Role Aktif</p>
                            <p class="text-xs md:text-sm font-black truncate">{{ $roleLabel }}</p>
                        </div>
                    </div>

                    <div class="glass-card px-4 py-3 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 bg-white">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shrink-0">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Cakupan Data</p>
                            <p class="text-xs md:text-sm font-black text-[#091E6E] truncate">{{ $scopeLabel }}</p>
                        </div>
                    </div>

                    <div class="glass-card px-4 py-3 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 bg-white">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shrink-0">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Pembaruan Terakhir</p>
                            <p class="text-xs md:text-sm font-black text-[#091E6E] truncate">{{ $lastUpdateText }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-5">

        <div class="group rounded-[1.8rem] p-5 md:p-6 border border-white shadow-sm bg-gradient-to-br from-white to-blue-50/60 hover:shadow-xl transition-all">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.25em] text-blue-500">QCC Total</p>
                    <h3 class="mt-2 text-3xl md:text-4xl font-black text-[#091E6E] leading-none">{{ $jumlahQcc }}</h3>
                    <p class="mt-2 text-[10px] md:text-xs text-gray-500 leading-relaxed">Total circle yang masuk dalam scope akses Anda.</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-[#091E6E] group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-users-gear text-xl"></i>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-blue-100 flex items-center justify-between text-[10px] md:text-xs">
                <span class="font-bold text-gray-400 uppercase tracking-wider">Update</span>
                <span class="font-bold text-blue-700">{{ $latestQccText }}</span>
            </div>
        </div>

        <div class="group rounded-[1.8rem] p-5 md:p-6 border border-white shadow-sm bg-gradient-to-br from-white to-indigo-50/60 hover:shadow-xl transition-all">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.25em] text-indigo-500">QCC Members</p>
                    <h3 class="mt-2 text-3xl md:text-4xl font-black text-[#091E6E] leading-none">{{ $jumlahQccMembers }}</h3>
                    <p class="mt-2 text-[10px] md:text-xs text-gray-500 leading-relaxed">Total anggota yang terlibat di semua circle.</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-indigo-600 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-user-group text-xl"></i>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-indigo-100 flex items-center justify-between text-[10px] md:text-xs">
                <span class="font-bold text-gray-400 uppercase tracking-wider">Avg / Circle</span>
                <span class="font-bold text-indigo-700">{{ $qccAvgMembers }}</span>
            </div>
        </div>

        <div class="group rounded-[1.8rem] p-5 md:p-6 border border-white shadow-sm bg-gradient-to-br from-white to-amber-50/60 hover:shadow-xl transition-all">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.25em] text-amber-500">SS Total</p>
                    <h3 class="mt-2 text-3xl md:text-4xl font-black text-[#091E6E] leading-none">{{ $jumlahSs }}</h3>
                    <p class="mt-2 text-[10px] md:text-xs text-gray-500 leading-relaxed">Total ide yang terdaftar sesuai otorisasi Anda.</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-amber-500 group-hover:scale-105 transition-transform">
                    <i class="fa-regular fa-lightbulb text-xl"></i>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-amber-100 flex items-center justify-between text-[10px] md:text-xs">
                <span class="font-bold text-gray-400 uppercase tracking-wider">Update</span>
                <span class="font-bold text-amber-700">{{ $latestSsText }}</span>
            </div>
        </div>

        <div class="group rounded-[1.8rem] p-5 md:p-6 border border-white shadow-sm bg-gradient-to-br from-white to-slate-50 hover:shadow-xl transition-all">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500">Completion Rate</p>
                    <h3 class="mt-2 text-3xl md:text-4xl font-black text-[#091E6E] leading-none">{{ $completionRate }}%</h3>
                    <p class="mt-2 text-[10px] md:text-xs text-gray-500 leading-relaxed">Persentase SS yang sudah approved / rewarded.</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-slate-600 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-chart-line text-xl"></i>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-[10px] md:text-xs">
                <span class="font-bold text-gray-400 uppercase tracking-wider">Approved</span>
                <span class="font-bold text-slate-700">{{ $jumlahSsApproved }}</span>
            </div>
        </div>
    </div>

    <!-- INSIGHT SECTION -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-6">
        <!-- QCC SUMMARY -->
        <div class="xl:col-span-2 rounded-[2rem] border border-white shadow-sm bg-white p-5 md:p-6">
            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.25em] text-[#091E6E]">Operational Snapshot</p>
                    <h3 class="mt-1 text-lg md:text-xl font-bold text-[#091E6E]">Ringkasan Quality Control Circle</h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                <div class="rounded-2xl border border-gray-100 p-4 bg-slate-50">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Circle</p>
                    <p class="mt-2 text-2xl font-black text-[#091E6E]">{{ $jumlahQcc }}</p>
                    <p class="mt-1 text-[10px] text-gray-500">Circle yang berada di scope akses Anda.</p>
                </div>

                <div class="rounded-2xl border border-gray-100 p-4 bg-slate-50">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Anggota Terlibat</p>
                    <p class="mt-2 text-2xl font-black text-indigo-600">{{ $jumlahQccMembers }}</p>
                    <p class="mt-1 text-[10px] text-gray-500">Total partisipasi anggota lintas circle.</p>
                </div>

                <div class="rounded-2xl border border-gray-100 p-4 bg-slate-50">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Rata-rata Anggota / Circle</p>
                    <p class="mt-2 text-2xl font-black text-blue-600">{{ $qccAvgMembers }}</p>
                    <p class="mt-1 text-[10px] text-gray-500">Rata-rata komposisi tim di setiap circle.</p>
                </div>

                <div class="rounded-2xl border border-gray-100 p-4 bg-slate-50">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Circle Bulan Ini</p>
                    <p class="mt-2 text-2xl font-black text-emerald-600">{{ $jumlahQccThisMonth }}</p>
                    <p class="mt-1 text-[10px] text-gray-500">Circle yang baru aktif pada bulan berjalan.</p>
                </div>
            </div>

            <div class="mt-5">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider">Kontribusi Circle Bulan Ini</span>
                    <span class="text-[10px] md:text-xs font-black text-blue-700">
                        {{ $jumlahQcc > 0 ? round(($jumlahQccThisMonth / $jumlahQcc) * 100) : 0 }}%
                    </span>
                </div>
                <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full bg-blue-600" style="width: {{ $progressWidth($jumlahQcc > 0 ? ($jumlahQccThisMonth / $jumlahQcc) * 100 : 0) }}%"></div>
                </div>
            </div>
        </div>

        <!-- SS SUMMARY -->
        <div class="rounded-[2rem] border border-white shadow-sm bg-white p-5 md:p-6">
            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.25em] text-[#091E6E]">Idea Generation</p>
                    <h3 class="mt-1 text-lg md:text-xl font-bold text-[#091E6E]">Ringkasan Suggestion System</h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-regular fa-lightbulb"></i>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider">Approved / Rewarded</span>
                        <span class="text-[10px] md:text-xs font-black text-emerald-600">{{ $jumlahSsApproved }} data</span>
                    </div>
                    <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ $progressWidth($completionRate) }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider">Pending Review</span>
                        <span class="text-[10px] md:text-xs font-black text-amber-600">{{ $jumlahSsPending }} data</span>
                    </div>
                    <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full bg-amber-500" style="width: {{ $progressWidth($pendingRate) }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-wider">Rejected</span>
                        <span class="text-[10px] md:text-xs font-black text-red-600">{{ $jumlahSsRejected }} data</span>
                    </div>
                    <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full bg-red-500" style="width: {{ $progressWidth($rejectedRate) }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 pt-2">
                    <div class="rounded-2xl border border-gray-100 p-4 bg-slate-50">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total SS Bulan Ini</p>
                        <p class="mt-2 text-2xl font-black text-[#091E6E]">{{ $jumlahSsThisMonth }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 p-4 bg-white">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Status Akun</p>
                        <p class="mt-2 text-sm font-black text-[#091E6E] uppercase">{{ $user->occupation }}</p>
                        <p class="mt-1 text-[10px] text-gray-500">{{ $scopeDescription }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ACCESS CONTEXT -->
    <div class="rounded-[2rem] border border-white shadow-sm bg-white p-5 md:p-6">
        <div class="flex items-center justify-between gap-4 mb-5">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-[#091E6E]">Access Context</p>
                <h3 class="mt-1 text-lg md:text-xl font-bold text-[#091E6E]">Cakupan Akses dan Ringkasan Operasional</h3>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Scope</p>
                <p class="mt-1 text-sm font-black text-[#091E6E]">{{ $scopeLabel }}</p>
                <p class="mt-2 text-xs text-gray-500 leading-relaxed">{{ $scopeDescription }}</p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Department Coverage</p>
                <p class="mt-1 text-sm font-black text-[#091E6E]">{{ $jumlahDeptTercakup }} Departemen</p>
                <p class="mt-2 text-xs text-gray-500 leading-relaxed">Jumlah departemen yang tercakup oleh akses Anda.</p>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Periode</p>
                <p class="mt-1 text-sm font-black text-[#091E6E]">{{ date('Y') }}</p>
                <p class="mt-2 text-xs text-gray-500 leading-relaxed">Dashboard aktif untuk tahun berjalan.</p>
            </div>
        </div>
    </div>

    <!-- FOOTER NOTE -->
    <div class="rounded-[1.5rem] border border-gray-100 bg-white shadow-sm p-4 md:p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#091E6E] text-white flex items-center justify-center shrink-0">
                <i class="fa-solid fa-circle-info text-sm"></i>
            </div>
            <p class="text-[10px] md:text-xs text-gray-500 leading-relaxed">
                Dashboard ini menampilkan ringkasan operasional sesuai peran aktif Anda di SIGITA. Gunakan data ini untuk monitoring harian, evaluasi progres, dan pengambilan keputusan.
            </p>
        </div>

        <div class="text-[10px] md:text-xs font-bold uppercase tracking-wider text-gray-400">
            Tahun Periode: <span class="text-[#091E6E]">{{ date('Y') }}</span>
        </div>
    </div>

</div>
@endsection
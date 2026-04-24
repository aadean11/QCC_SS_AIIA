@extends('welcome')

@section('title', 'Daftar SS Saya')

@section('content')
<div class="animate-reveal">
    <!-- Breadcrumb -->
    <nav class="flex mb-4 md:mb-6 text-xs md:text-sm text-gray-400">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">SS</li>
            <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
            <li class="text-[#091E6E] font-semibold tracking-tight text-[10px] md:text-xs">Daftar SS Saya</li>
        </ol>
    </nav>

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Daftar SS Saya</h2>
            <p class="text-xs md:text-sm text-gray-400">Riwayat Suggestion System yang Anda ajukan</p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full md:w-auto justify-start md:justify-end items-center">
            <form action="{{ route('ss.karyawan.index') }}" method="GET" id="filterForm" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm transition-all hover:border-[#091E6E] w-full sm:w-auto">
                    <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent w-full sm:w-auto">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ide / status..." 
                        class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm transition-all text-xs md:text-sm font-medium">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] md:text-xs"></i>
                </div>
            </form>

            <a href="{{ route('ss.karyawan.create') }}" class="bg-[#091E6E] hover:bg-[#130998] text-white px-4 md:px-5 py-2 rounded-xl flex items-center gap-2 shadow-lg transition-all active:scale-95 text-[10px] md:text-xs font-bold uppercase tracking-wider w-full sm:w-auto justify-center">
                <i class="fa-solid fa-plus"></i> Upload SS Baru
            </a>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-5 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[900px] md:min-w-full">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-2 md:px-4 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Tanggal Upload</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">File PDF</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Score</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Status</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Reward</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $index => $ss)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all group shadow-sm border border-gray-100">
                        <td class="px-2 md:px-4 py-2 md:py-3 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500 text-xs md:text-sm">
                            {{ ($submissions->currentPage() - 1) * $submissions->perPage() + $loop->iteration }}
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            <span class="font-medium text-gray-700 text-xs md:text-sm">{{ \Carbon\Carbon::parse($ss->submission_date)->format('d/m/Y H:i') }}</span>
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            <a href="{{ asset('storage/' . $ss->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1 text-xs md:text-sm">
                                <i class="fa-regular fa-file-pdf"></i> Lihat PDF
                            </a>
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            @if($ss->score !== null)
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-bold">{{ $ss->score }}</span>
                            @else
                                <span class="text-gray-400 italic text-xs">-</span>
                            @endif
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            @php
                                $statusColors = [
                                    'draft' => 'gray',
                                    'submitted' => 'yellow',
                                    'assessed' => 'blue',
                                    'spv_review' => 'purple',
                                    'kdp_review' => 'orange',
                                    'approved' => 'green',
                                    'rejected' => 'red',
                                    'rewarded' => 'emerald'
                                ];
                                $color = $statusColors[$ss->status] ?? 'gray';
                            @endphp
                            <span class="bg-{{ $color }}-100 text-{{ $color }}-800 px-2 py-1 rounded-full text-xs font-semibold uppercase">
                                {{ str_replace('_', ' ', $ss->status) }}
                            </span>
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            @if($ss->reward_amount)
                                <span class="text-emerald-600 font-bold text-xs md:text-sm">Rp {{ number_format($ss->reward_amount, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400 italic text-xs">-</span>
                            @endif
                        </td>

                        <td class="px-3 md:px-6 py-2 md:py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <div class="flex justify-center gap-1 md:gap-2">
                                <a href="{{ route('ss.karyawan.show', $ss->id) }}" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Detail">
                                    <i class="fa-solid fa-eye text-[8px] md:text-[10px]"></i>
                                </a>
                            </div>
                        </td>
                     </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-300 italic text-xs md:text-sm">Belum ada pengajuan SS.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION AREA -->
        @if($submissions->hasPages())
        <div class="mt-4 md:mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-4 md:pt-6">
            <div class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $submissions->firstItem() ?? 0 }} to {{ $submissions->lastItem() ?? 0 }} of {{ $submissions->total() }} entries
            </div>
            <div class="custom-pagination">
                {{ $submissions->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Styling Paging Horizontal - sama seperti di Master Seven Tools */
    .custom-pagination nav { display: flex; align-items: center; justify-content: center; gap: 4px; }
    .custom-pagination nav svg { width: 0.875rem; height: 0.875rem; }
    @media (min-width: 768px) {
        .custom-pagination nav svg { width: 1rem; height: 1rem; }
    }
    .custom-pagination span[aria-current="page"] > span { 
        background-color: #091E6E !important; color: white !important; border: none !important;
        border-radius: 6px !important; padding: 4px 10px !important; font-size: 10px !important; font-weight: 800;
        box-shadow: 0 4px 6px -1px rgba(9, 30, 110, 0.2);
    }
    .custom-pagination a, .custom-pagination span { 
        border-radius: 6px !important; padding: 4px 10px !important; font-size: 10px !important;
        font-weight: 700 !important; border: 1px solid #edf2f7 !important; color: #64748b;
        transition: all 0.2s ease;
    }
    @media (min-width: 768px) {
        .custom-pagination span[aria-current="page"] > span { padding: 6px 12px !important; font-size: 11px !important; }
        .custom-pagination a, .custom-pagination span { padding: 6px 12px !important; font-size: 11px !important; }
    }
    .custom-pagination a:hover { background-color: #f8fafc !important; border-color: #091E6E !important; color: #091E6E !important; }
</style>
@endpush
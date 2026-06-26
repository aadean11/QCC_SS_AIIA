@extends('welcome')

@section('title', 'Review Komite SS')

@section('content')
<div class="animate-reveal">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Monitoring SS', 'icon' => 'fa-regular fa-lightbulb'],
        'Review Komite',
    ]])

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Review Komite SS</h2>
            <p class="text-xs md:text-sm text-gray-400">Persetujuan akhir komite dan persiapan pemberian reward</p>
        </div>

        <form action="{{ route('ss.admin.review.index') }}" method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm hover:border-[#091E6E] w-full sm:w-auto">
                <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">Show</span>
                <select name="per_page" onchange="this.form.submit()" class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none bg-transparent w-full sm:w-auto">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengaju/dept..."
                    class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#091E6E] shadow-sm text-xs md:text-sm font-medium">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] md:text-xs"></i>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-5 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[900px] md:min-w-full">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-2 md:px-4 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Pengaju</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Departemen</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Nilai</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold">Tanggal</th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center text-white text-[8px] md:text-[10px] uppercase tracking-[0.2em] font-bold rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $ss)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100">
                        <td class="px-2 md:px-4 py-2 md:py-3 rounded-l-xl border-y border-l border-gray-100 text-center font-bold text-gray-500 text-xs md:text-sm">
                            {{ ($submissions->currentPage() - 1) * $submissions->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            <span class="font-semibold text-[#091E6E] text-xs md:text-sm">{{ $ss->employee->nama ?? $ss->employee_npk }}</span>
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100 text-xs md:text-sm text-gray-600">{{ $ss->department_code }}</td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100">
                            <span class="bg-orange-50 text-orange-700 px-2 py-1 rounded-full text-xs font-bold">{{ $ss->kdp_score_total ?? $ss->spv_score_total ?? $ss->score ?? '-' }}</span>
                        </td>
                        <td class="px-3 md:px-6 py-2 md:py-3 border-y border-gray-100 text-xs md:text-sm text-gray-600">{{ optional($ss->updated_at)->format('d/m/Y H:i') }}</td>
                        <td class="px-3 md:px-6 py-2 md:py-3 rounded-r-xl border-y border-r border-gray-100 text-center">
                            <a href="{{ route('ss.admin.review.form', $ss->id) }}" class="w-8 h-8 inline-flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Review">
                                <i class="fa-solid fa-clipboard-check text-[10px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-300 italic text-xs md:text-sm">Tidak ada SS yang menunggu review komite.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
        <div class="mt-4 md:mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-50 pt-4 md:pt-6">
            <div class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">
                Showing {{ $submissions->firstItem() ?? 0 }} to {{ $submissions->lastItem() ?? 0 }} of {{ $submissions->total() }} entries
            </div>
            <div class="custom-pagination">{{ $submissions->links('pagination::tailwind') }}</div>
        </div>
        @endif
    </div>
</div>
@endsection

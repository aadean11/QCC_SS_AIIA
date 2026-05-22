@extends('welcome')

@section('title', 'Review SS - KDP')

@section('content')
<div class="animate-reveal">
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Monitoring SS', 'icon' => 'fa-regular fa-lightbulb'],
        'Review SS (KDP)',
    ]])

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-[#091E6E]">Review SS — KDP</h2>
            <p class="text-xs md:text-sm text-gray-400 italic font-medium">
                Departemen:
                <span class="text-[#1035D1] uppercase font-bold">
                    @php $myDept = $user->getDepartment(); @endphp
                    {{ $myDept ? $myDept->name : ($user->getDeptCode() ?: 'TIDAK TERDETEKSI') }}
                </span>
            </p>
        </div>

        <form action="{{ route('ss.approval.kdp') }}" method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 shadow-sm w-full sm:w-auto">
                <span class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase">Show</span>
                <select name="per_page" onchange="this.form.submit()" class="text-[10px] md:text-xs font-bold text-[#091E6E] outline-none cursor-pointer bg-transparent">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengaju..."
                    class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#091E6E] text-xs md:text-sm font-medium">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 shadow-sm border border-white">
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-separate border-spacing-y-2 min-w-[800px] md:min-w-full">
                <thead>
                    <tr class="sidebar-gradient shadow-md">
                        <th class="px-2 md:px-4 py-3 text-white text-[8px] md:text-[10px] uppercase font-bold rounded-tl-2xl text-center w-12">No</th>
                        <th class="px-3 md:px-6 py-3 text-white text-[8px] md:text-[10px] uppercase font-bold">Pengaju & Dept</th>
                        <th class="px-3 md:px-6 py-3 text-white text-[8px] md:text-[10px] uppercase font-bold text-center">Score</th>
                        <th class="px-3 md:px-6 py-3 text-white text-[8px] md:text-[10px] uppercase font-bold">Review SPV</th>
                        <th class="px-3 md:px-6 py-3 text-white text-[8px] md:text-[10px] uppercase font-bold text-center rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $ss)
                    <tr class="bg-white hover:bg-blue-50/50 transition-all shadow-sm border border-gray-100">
                        <td class="px-2 md:px-4 py-3 rounded-l-xl border-y border-l text-center font-bold text-gray-500 text-xs">
                            {{ ($submissions->currentPage() - 1) * $submissions->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-3 md:px-6 py-3 border-y">
                            <p class="font-bold text-[#091E6E] text-xs md:text-sm">{{ $ss->employee->nama ?? $ss->employee_npk }}</p>
                            <p class="text-[9px] text-gray-400 font-bold uppercase mt-0.5">{{ $ss->department?->name ?? $ss->department_code }}</p>
                        </td>
                        <td class="px-3 md:px-6 py-3 border-y text-center">
                            <span class="bg-blue-50 text-[#091E6E] font-black px-3 py-1 rounded-lg text-xs md:text-sm">{{ $ss->score ?? '-' }}</span>
                        </td>
                        <td class="px-3 md:px-6 py-3 border-y">
                            <span class="text-[10px] text-gray-600">{{ $ss->spv_notes ? \Illuminate\Support\Str::limit($ss->spv_notes, 40) : '-' }}</span>
                        </td>
                        <td class="px-3 md:px-6 py-3 rounded-r-xl border-y border-r text-center">
                            <div class="flex flex-wrap justify-center gap-1 md:gap-2">
                                @if($ss->file_path)
                                <a href="{{ asset('storage/'.$ss->file_path) }}" target="_blank"
                                    class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all" title="Lihat PDF">
                                    <i class="fa-solid fa-file-pdf text-xs"></i>
                                </a>
                                @endif
                                <a href="{{ route('ss.approval.kdp.review', $ss->id) }}"
                                    class="bg-orange-600 text-white px-4 py-2 rounded-xl text-[10px] font-bold uppercase hover:bg-orange-700 transition-all">
                                    Review
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-16 text-gray-300 italic text-sm">
                            <i class="fa-regular fa-lightbulb text-4xl block mb-2 opacity-30"></i>
                            Tidak ada ide SS yang menunggu review KDP.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t pt-6">
            <div class="text-[10px] font-bold text-gray-400 uppercase">
                Showing {{ $submissions->firstItem() ?? 0 }} to {{ $submissions->lastItem() ?? 0 }} of {{ $submissions->total() }} entries
            </div>
            <div class="custom-pagination">{{ $submissions->links('pagination::tailwind') }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(Session::has('success'))
<script>
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: @json(Session::get('success')), timer: 2500, showConfirmButton: false });
</script>
@endif
@endpush

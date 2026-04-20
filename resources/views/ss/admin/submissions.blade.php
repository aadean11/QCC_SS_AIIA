@extends('welcome')

@section('title', 'Daftar Ide SS')

@section('content')
<div class="animate-reveal pb-20">
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
        <nav class="flex text-xs md:text-sm text-gray-400">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center text-gray-400">SS</li>
                <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
                <li class="text-[#091E6E] font-semibold tracking-tight uppercase text-[10px] md:text-xs">Daftar Ide</li>
            </ol>
        </nav>
        <form method="GET" class="flex gap-3">
            <select name="status" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="submitted" {{ request('status')=='submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="assessed" {{ request('status')=='assessed' ? 'selected' : '' }}>Assessed (Need SPV)</option>
                <option value="spv_review" {{ request('status')=='spv_review' ? 'selected' : '' }}>SPV Review</option>
                <option value="kdp_review" {{ request('status')=='kdp_review' ? 'selected' : '' }}>KDP Review</option>
                <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="rewarded" {{ request('status')=='rewarded' ? 'selected' : '' }}>Rewarded</option>
            </select>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-4 md:p-6 shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50/50">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Pengaju</th>
                    <th class="px-4 py-3 text-left">Departemen</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Score</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Reward</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($submissions as $ss)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">{{ $ss->employee->nama ?? $ss->employee_npk }}</td>
                    <td class="px-4 py-3">{{ $ss->department_code }}</td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($ss->submission_date)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-bold">{{ $ss->score ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @php $c = ['submitted'=>'yellow','assessed'=>'blue','spv_review'=>'purple','kdp_review'=>'orange','approved'=>'green','rejected'=>'red','rewarded'=>'emerald']; @endphp
                        <span class="bg-{{ $c[$ss->status] ?? 'gray' }}-100 text-{{ $c[$ss->status] ?? 'gray' }}-800 px-2 py-0.5 rounded-full text-xs">{{ str_replace('_', ' ', $ss->status) }}</span>
                    </td>
                    <td class="px-4 py-3">{{ $ss->reward_amount ? 'Rp '.number_format($ss->reward_amount) : '-' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('ss.admin.show', $ss->id) }}" class="text-blue-600 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-8 text-gray-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $submissions->appends(request()->query())->links() }}</div>
    </div>
</div>
@endsection
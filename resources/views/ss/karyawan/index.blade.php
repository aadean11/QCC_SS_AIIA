@extends('welcome')

@section('title', 'Daftar SS Saya')

@section('content')
<div class="animate-reveal pb-20">
    <!-- Breadcrumbs -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
        <nav class="flex text-xs md:text-sm text-gray-400">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center text-gray-400">SS</li>
                <li><i class="fa-solid fa-chevron-right text-[8px] md:text-[10px] mx-1 md:mx-2"></i></li>
                <li class="text-[#091E6E] font-semibold tracking-tight uppercase text-[10px] md:text-xs">Daftar SS Saya</li>
            </ol>
        </nav>
        <a href="{{ route('ss.karyawan.create') }}" class="bg-[#091E6E] hover:bg-[#130998] text-white text-xs md:text-sm font-bold py-2 px-4 rounded-xl transition-all shadow-md flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Upload SS Baru
        </a>
    </div>

    <!-- Tabel Data -->
    <div class="glass-card rounded-2xl p-4 md:p-6 shadow-sm border border-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Upload</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">File PDF</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reward</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($submissions as $index => $ss)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($ss->submission_date)->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ asset('storage/' . $ss->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">
                                <i class="fa-regular fa-file-pdf"></i> Lihat PDF
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm font-bold">
                            @if($ss->score !== null)
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">{{ $ss->score }}</span>
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
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
                        <td class="px-4 py-3 text-sm font-bold text-green-600">
                            {{ $ss->reward_amount ? 'Rp ' . number_format($ss->reward_amount, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('ss.karyawan.show', $ss->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fa-regular fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-400 italic">Belum ada pengajuan SS.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
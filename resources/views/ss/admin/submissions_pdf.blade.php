<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Ide SS</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #111827; }
        h1 { margin: 0 0 4px; font-size: 18px; color: #091E6E; text-align: center; }
        .subtitle { text-align: center; color: #4b5563; margin-bottom: 14px; }
        .meta { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .meta td { padding: 3px 6px; border: 1px solid #e5e7eb; }
        .meta .label { width: 110px; font-weight: bold; background: #f3f4f6; color: #374151; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #091E6E; color: #fff; padding: 6px 5px; border: 1px solid #091E6E; text-align: left; }
        table.data td { padding: 5px; border: 1px solid #d1d5db; vertical-align: top; }
        table.data tbody tr:nth-child(even) { background: #f9fafb; }
        .center { text-align: center; }
        .right { text-align: right; }
        .empty { text-align: center; padding: 18px; color: #6b7280; font-style: italic; }
        .signatures { width: 100%; margin-top: 34px; border-collapse: collapse; page-break-inside: avoid; }
        .signatures td { width: 50%; text-align: center; padding: 0 28px; vertical-align: bottom; }
        .signature-title { font-weight: bold; margin-bottom: 62px; }
        .signature-line { border-top: 1px solid #111827; padding-top: 6px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Daftar Ide Suggestion System</h1>
    <div class="subtitle">Dicetak pada {{ now()->format('d/m/Y H:i') }}</div>

    @php
        $dateColumnLabel = match($filters['status'] ?? null) {
            'approved'     => 'Tgl Approved',
            'rewarded'     => 'Tgl Reward Dibayar',
            'admin_review' => 'Tgl Admin Review',
            'kdp_review'   => 'Tgl KDP Review',
            'spv_review'   => 'Tgl SPV Review',
            default        => 'Tgl Pengajuan',
        };
    @endphp
    <table class="meta">
        <tr>
            <td class="label">Departemen</td>
            <td>{{ $filters['department'] ? $filters['department']->name.' ('.$filters['department']->code.')' : 'Semua Departemen' }}</td>
            <td class="label">Periode ({{ $dateColumnLabel }})</td>
            <td>
                @if($filters['date_from'] && $filters['date_to'])
                    {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}
                @elseif($filters['date_from'])
                    Dari {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }}
                @elseif($filters['date_to'])
                    Sampai {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}
                @else
                    Semua Periode
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td>{{ $filters['status'] ? strtoupper(str_replace('_', ' ', $filters['status'])) : 'Semua Status' }}</td>
            <td class="label">Pencarian</td>
            <td>{{ $filters['search'] ?: '-' }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="center" style="width: 28px;">No</th>
                <th style="width: 110px;">Pengaju</th>
                <th class="center" style="width: 80px;">{{ $dateColumnLabel }}</th>
                <th style="width: 100px;">Departemen</th>
                <th class="center" style="width: 60px;">Jumlah Ide</th>
                <th class="right" style="width: 90px;">Total Reward</th>
            </tr>
        </thead>
        <tbody>
            @php
                $groupedSubmissions = $submissions->groupBy('employee_npk');
            @endphp
            @forelse($groupedSubmissions as $npk => $employeeSubmissions)
                @php
                    $firstSubmission = $employeeSubmissions->first();
                    $totalReward = $employeeSubmissions->sum(function($sub) {
                        return $sub->reward_amount ?? $sub->calculated_reward_amount ?? 0;
                    });
                @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $firstSubmission->employee->nama ?? $firstSubmission->employee_npk }}</td>
                    <td class="center">
                        @php
                            $relevantDate = match($filters['status'] ?? null) {
                                'approved'     => $firstSubmission->final_approved_at,
                                'rewarded'     => $firstSubmission->paid_at,
                                'admin_review' => $firstSubmission->admin_approved_at,
                                'kdp_review'   => $firstSubmission->kdp_approved_at,
                                'spv_review'   => $firstSubmission->spv_approved_at,
                                default        => $firstSubmission->submission_date,
                            };
                        @endphp
                        {{ $relevantDate ? \Carbon\Carbon::parse($relevantDate)->format('d/m/Y') : '-' }}
                        @if(($filters['status'] ?? null) && ($filters['status'] !== 'submitted') && $firstSubmission->submission_date)
                            <br><span style="font-size:8px;color:#6b7280;">Diajukan: {{ \Carbon\Carbon::parse($firstSubmission->submission_date)->format('d/m/Y') }}</span>
                        @endif
                    </td>
                    <td>{{ $firstSubmission->department?->name ?? $firstSubmission->department_code }}</td>
                    <td class="center">{{ $employeeSubmissions->count() }}</td>
                    <td class="right">
                        Rp {{ number_format($totalReward, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">Tidak ada data SS sesuai filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <div class="signature-title">Mengetahui,</div>
                <div class="signature-line">GM</div>
            </td>
            <td>
                <div class="signature-title">Menyetujui,</div>
                <div class="signature-line">Presiden Direktur</div>
            </td>
        </tr>
    </table>
</body>
</html>

<?php

namespace App\Console\Commands;

use App\Models\QccPeriod;
use App\Models\QccTarget;
use App\Models\QccCircle;
use App\Models\Employee;
use App\Models\QccPeriodStep;
use App\Models\QccCircleStepTransaction;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SendQccStepEWS extends Command
{
    protected $signature = 'qcc:send-step-ews';
    protected $description = 'Kirim notifikasi WA ke supervisor jika ada circle yang terlambat mencapai target step berdasarkan deadline';

    public function handle(WhatsAppService $wa)
    {
        $today = Carbon::today('Asia/Jakarta');
        $notificationDate = $today->copy()->subDay();
        $periods = QccPeriod::where('status', 'ACTIVE')->get();

        foreach ($periods as $period) {
            // EWS hanya dikirim H+1 dari deadline tiap step agar tidak spam harian.
            $periodSteps = QccPeriodStep::where('qcc_period_id', $period->id)
                ->with('step')
                ->get()
                ->sortBy(function ($ps) {
                    return $ps->step->step_number ?? 0;
                })
                ->filter(function ($ps) use ($notificationDate) {
                    return Carbon::parse($ps->deadline_date)->isSameDay($notificationDate);
                });

            if ($periodSteps->isEmpty()) {
                $this->info("Periode {$period->period_name}: tidak ada deadline step H+1 pada {$notificationDate->format('d/m/Y')}.");
                continue;
            }

            foreach ($periodSteps as $targetStep) {
                $stepNumber = $targetStep->step->step_number;
                $stepId = $targetStep->qcc_step_id;

                $targets = QccTarget::where('qcc_period_id', $period->id)->get();

                foreach ($targets as $target) {
                    $deptCode = $target->department_code;
                    $deptName = $target->department->name ?? $deptCode;

                    $circles = QccCircle::where('qcc_period_id', $period->id)
                        ->where('department_code', $deptCode)
                        ->get();

                    $lateCircles = [];
                    foreach ($circles as $circle) {
                        $isAchieved = false;
                        if ($stepNumber == 0) {
                            $isAchieved = ($circle->status === 'ACTIVE');
                        } else {
                            $transaction = QccCircleStepTransaction::where('qcc_circle_id', $circle->id)
                                ->where('qcc_step_id', $stepId)
                                ->first();
                            $isAchieved = ($transaction && $transaction->status === 'APPROVED');
                        }
                        if (!$isAchieved) {
                            $lateCircles[] = $circle;
                        }
                    }

                    $lateCount = count($lateCircles);
                    if ($lateCount == 0) {
                        $this->info("Dept {$deptCode}: semua circle sudah mencapai step {$stepNumber}");
                        continue;
                    }

                    $supervisors = Employee::inDepartment($deptCode)
                        ->whereIn('occupation', ['SPV'])
                        ->whereNotNull('phone')
                        ->where('phone', '<>', '')
                        ->get();

                    if ($supervisors->isEmpty()) {
                        $this->warn("Tidak ada nomor WA untuk supervisor di dept {$deptCode}");
                        continue;
                    }

                    $message = $this->buildMessage($period, $deptName, $lateCount, $lateCircles);

                    foreach ($supervisors as $sup) {
                        $sent = $wa->sendMessage($sup->phone, $message);
                        $status = $sent ? 'dikirim' : 'gagal dikirim';
                        $this->info("Notifikasi H+1 step {$stepNumber} {$status} ke {$sup->nama} ({$sup->occupation}) - {$sup->phone}");
                        sleep(2);
                    }
                }
            }
        }

        $this->info('Pengecekan EWS step selesai.');
    }

    private function buildMessage($period, $deptName, $lateCount, array $lateCircles)
    {
        $circleNames = Collection::make($lateCircles)->pluck('circle_name')->implode(', ');

        return "QCC Periode {$period->period_name} ({$period->year}) Departemen {$deptName} terdapat {$lateCount} Circle yang terlambat diantaranya {$circleNames}.";
    }
}

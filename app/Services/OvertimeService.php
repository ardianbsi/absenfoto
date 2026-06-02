<?php

namespace App\Services;

use App\Enums\OvertimeStatus;
use App\Models\Employee;
use App\Models\OvertimeRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OvertimeService
{
    protected int $maxDailyMinutes;

    public function __construct(
        protected NotificationService $notificationService
    ) {
        $this->maxDailyMinutes = config('absensi.overtime.max_daily_minutes', 240);
    }

    public function submitOvertime(Employee $employee, array $data): OvertimeRequest
    {
        $date = Carbon::parse($data['date'])->toDateString();
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);

        if ($endTime->lte($startTime)) {
            throw new \RuntimeException('Waktu selesai harus setelah waktu mulai.');
        }

        $totalMinutes = $this->calculateOvertimeMinutes($startTime, $endTime);

        if ($totalMinutes > $this->maxDailyMinutes) {
            throw new \RuntimeException(
                'Lembur maksimal ' . ($this->maxDailyMinutes / 60) . ' jam per hari.'
            );
        }

        $overlap = OvertimeRequest::where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->whereIn('status', [OvertimeStatus::Pending->value, OvertimeStatus::Approved->value])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                          ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();

        if ($overlap) {
            throw new \RuntimeException('Sudah ada pengajuan lembur pada rentang waktu tersebut.');
        }

        $attachmentPath = null;
        if (isset($data['attachment']) && $data['attachment']->isValid()) {
            $attachmentPath = $data['attachment']->store('overtimes/' . $employee->id, 'public');
        }

        $overtime = OvertimeRequest::create([
            'employee_id'   => $employee->id,
            'date'          => $date,
            'start_time'    => $startTime,
            'end_time'      => $endTime,
            'total_minutes' => $totalMinutes,
            'description'   => $data['description'],
            'attachment'    => $attachmentPath,
            'status'        => OvertimeStatus::Pending->value,
        ]);

        $this->notifyOvertimePending($overtime);

        return $overtime;
    }

    public function approveOvertime(OvertimeRequest $overtime, User $approver): OvertimeRequest
    {
        if ($overtime->status !== OvertimeStatus::Pending->value) {
            throw new \RuntimeException('Pengajuan lembur ini sudah diproses.');
        }

        $overtime->update([
            'status'      => OvertimeStatus::Approved->value,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        try {
            $this->notificationService->sendOvertimeApproved($overtime);
        } catch (\Throwable $e) {
            report($e);
        }

        return $overtime->fresh();
    }

    public function rejectOvertime(OvertimeRequest $overtime, User $approver, string $reason): OvertimeRequest
    {
        if ($overtime->status !== OvertimeStatus::Pending->value) {
            throw new \RuntimeException('Pengajuan lembur ini sudah diproses.');
        }

        $overtime->update([
            'status'          => OvertimeStatus::Rejected->value,
            'approved_by'     => $approver->id,
            'approved_at'     => now(),
            'rejected_reason' => $reason,
        ]);

        try {
            $this->notificationService->sendOvertimeRejected($overtime);
        } catch (\Throwable $e) {
            report($e);
        }

        return $overtime->fresh();
    }

    public function calculateOvertimeMinutes(Carbon $startTime, Carbon $endTime): int
    {
        return (int) $startTime->diffInMinutes($endTime);
    }

    protected function notifyOvertimePending(OvertimeRequest $overtime): void
    {
        try {
            $manager = $overtime->employee->manager;
            if ($manager && $manager->user) {
                $manager->user->notify(new \App\Notifications\OvertimeRequestSubmitted($overtime));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}

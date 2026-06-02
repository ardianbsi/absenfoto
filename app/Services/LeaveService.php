<?php

namespace App\Services;

use App\Enums\LeaveStatus;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function submitLeave(Employee $employee, array $data): LeaveRequest
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        if ($endDate->lt($startDate)) {
            throw new \RuntimeException('Tanggal selesai harus setelah atau sama dengan tanggal mulai.');
        }

        $overlap = LeaveRequest::where('employee_id', $employee->id)
            ->whereIn('status', [LeaveStatus::Pending->value, LeaveStatus::Approved->value])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($overlap) {
            throw new \RuntimeException('Anda sudah memiliki pengajuan cuti/izin pada rentang tanggal tersebut.');
        }

        $leaveType = LeaveType::findOrFail($data['leave_type_id']);

        $totalDays = $startDate->diffInDays($endDate) + 1;

        if ($leaveType->is_deduct_quota) {
            $balance = $this->getLeaveBalance($employee, $leaveType);
            if ($totalDays > $balance) {
                throw new \RuntimeException(
                    'Sisa kuota ' . $leaveType->name . ' Anda tidak mencukupi. Sisa: ' . $balance . ' hari.'
                );
            }
        }

        $attachmentPath = null;
        if (isset($data['attachment']) && $data['attachment']->isValid()) {
            $attachmentPath = $data['attachment']->store('leaves/' . $employee->id, 'public');
        }

        $leaveRequest = LeaveRequest::create([
            'employee_id'  => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'total_days'   => $totalDays,
            'reason'       => $data['reason'],
            'attachment'   => $attachmentPath,
            'status'       => LeaveStatus::Pending->value,
        ]);

        $this->notifyLeavePending($leaveRequest);

        return $leaveRequest;
    }

    public function approveLeave(LeaveRequest $leave, User $approver): LeaveRequest
    {
        if ($leave->status !== LeaveStatus::Pending->value) {
            throw new \RuntimeException('Pengajuan cuti ini sudah diproses.');
        }

        DB::transaction(function () use ($leave, $approver) {
            $leave->update([
                'status'      => LeaveStatus::Approved->value,
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ]);

            if ($leave->leaveType && $leave->leaveType->is_deduct_quota) {
                $leave->employee->decrement('quota_' . $leave->leaveType->code, $leave->total_days);
            }
        });

        try {
            $this->notificationService->sendLeaveApproved($leave);
        } catch (\Throwable $e) {
            report($e);
        }

        return $leave->fresh();
    }

    public function rejectLeave(LeaveRequest $leave, User $approver, string $reason): LeaveRequest
    {
        if ($leave->status !== LeaveStatus::Pending->value) {
            throw new \RuntimeException('Pengajuan cuti ini sudah diproses.');
        }

        $leave->update([
            'status'           => LeaveStatus::Rejected->value,
            'approved_by'      => $approver->id,
            'approved_at'      => now(),
            'rejected_reason'  => $reason,
        ]);

        try {
            $this->notificationService->sendLeaveRejected($leave);
        } catch (\Throwable $e) {
            report($e);
        }

        return $leave->fresh();
    }

    public function getLeaveBalance(Employee $employee, LeaveType $leaveType): int
    {
        $usedDays = LeaveRequest::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('status', LeaveStatus::Approved->value)
            ->whereYear('start_date', now()->year)
            ->sum('total_days');

        return max(0, $leaveType->quota - $usedDays);
    }

    public function cancelLeave(LeaveRequest $leave): LeaveRequest
    {
        if ($leave->status === LeaveStatus::Approved->value) {
            if ($leave->leaveType && $leave->leaveType->is_deduct_quota) {
                $leave->employee->increment('quota_' . $leave->leaveType->code, $leave->total_days);
            }
        }

        $leave->update([
            'status' => LeaveStatus::Cancelled->value,
        ]);

        return $leave->fresh();
    }

    protected function notifyLeavePending(LeaveRequest $leave): void
    {
        try {
            $manager = $leave->employee->manager;
            if ($manager && $manager->user) {
                $manager->user->notify(new \App\Notifications\LeaveRequestSubmitted($leave));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}

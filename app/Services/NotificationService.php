<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\Shift;
use App\Notifications\CheckInReminder;
use App\Notifications\CheckOutReminder;
use App\Notifications\LateAlert;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRejected;
use App\Notifications\OvertimeApproved;
use App\Notifications\OvertimeRejected;
use App\Notifications\ShiftAssigned;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    public function sendLeaveApproved(LeaveRequest $leave): void
    {
        $employee = $leave->employee;

        if ($employee && $employee->user) {
            $employee->user->notify(new LeaveApproved($leave));

            $this->sendEmail($employee->user->email, 'Cuti Disetujui',
                "Pengajuan cuti {$leave->leaveType?->name} Anda telah disetujui."
            );

            $this->sendWhatsApp($employee->user->phone, "Pengajuan cuti {$leave->leaveType?->name} Anda telah disetujui.");
        }
    }

    public function sendLeaveRejected(LeaveRequest $leave): void
    {
        $employee = $leave->employee;

        if ($employee && $employee->user) {
            $employee->user->notify(new LeaveRejected($leave));

            $this->sendEmail($employee->user->email, 'Cuti Ditolak',
                "Pengajuan cuti {$leave->leaveType?->name} Anda ditolak. Alasan: {$leave->rejected_reason}"
            );

            $this->sendWhatsApp($employee->user->phone,
                "Pengajuan cuti {$leave->leaveType?->name} Anda ditolak. Alasan: {$leave->rejected_reason}"
            );
        }
    }

    public function sendOvertimeApproved(OvertimeRequest $overtime): void
    {
        $employee = $overtime->employee;

        if ($employee && $employee->user) {
            $employee->user->notify(new OvertimeApproved($overtime));

            $this->sendEmail($employee->user->email, 'Lembur Disetujui',
                "Pengajuan lembur Anda pada {$overtime->date} telah disetujui."
            );

            $this->sendWhatsApp($employee->user->phone,
                "Pengajuan lembur Anda pada {$overtime->date} telah disetujui."
            );
        }
    }

    public function sendOvertimeRejected(OvertimeRequest $overtime): void
    {
        $employee = $overtime->employee;

        if ($employee && $employee->user) {
            $employee->user->notify(new OvertimeRejected($overtime));

            $this->sendEmail($employee->user->email, 'Lembur Ditolak',
                "Pengajuan lembur Anda pada {$overtime->date} ditolak. Alasan: {$overtime->rejected_reason}"
            );

            $this->sendWhatsApp($employee->user->phone,
                "Pengajuan lembur Anda pada {$overtime->date} ditolak. Alasan: {$overtime->rejected_reason}"
            );
        }
    }

    public function sendLateAlert(Attendance $attendance): void
    {
        $employee = $attendance->employee;
        $minutes = $attendance->late_minutes;

        if ($employee && $employee->user) {
            $employee->user->notify(new LateAlert($attendance));

            $this->sendWhatsApp($employee->user->phone,
                "Anda telat {$minutes} menit pada {$attendance->date}. Harap lebih tepat waktu."
            );
        }

        $manager = $employee?->manager;
        if ($manager && $manager->user) {
            $manager->user->notify(new LateAlert($attendance));
        }
    }

    public function sendCheckInReminder(Employee $employee): void
    {
        if ($employee->user) {
            $employee->user->notify(new CheckInReminder($employee));

            $this->sendWhatsApp($employee->user->phone,
                "Jangan lupa melakukan check-in hari ini."
            );
        }
    }

    public function sendCheckOutReminder(Employee $employee): void
    {
        if ($employee->user) {
            $employee->user->notify(new CheckOutReminder($employee));

            $this->sendWhatsApp($employee->user->phone,
                "Jangan lupa melakukan check-out hari ini."
            );
        }
    }

    public function sendShiftAssignment(Employee $employee, Shift $shift): void
    {
        if ($employee->user) {
            $employee->user->notify(new ShiftAssigned($employee, $shift));

            $this->sendWhatsApp($employee->user->phone,
                "Jadwal shift Anda: {$shift->name} ({$shift->start_time?->format('H:i')} - {$shift->end_time?->format('H:i')})"
            );
        }
    }

    protected function sendEmail(string $email, string $subject, string $message): void
    {
        try {
            \Illuminate\Support\Facades\Mail::raw($message, function ($mail) use ($email, $subject) {
                $mail->to($email)
                    ->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim email ke ' . $email . ': ' . $e->getMessage());
        }
    }

    protected function sendWhatsApp(?string $phone, string $message): void
    {
        if (empty($phone)) {
            return;
        }

        try {
            $apiKey = config('absensi.whatsapp.api_key');
            $sender = config('absensi.whatsapp.sender');

            if ($apiKey && $sender) {
                $http = new \Illuminate\Http\Client\PendingRequest();
                $http->timeout(10)->post(config('absensi.whatsapp.api_url'), [
                    'api_key' => $apiKey,
                    'sender'  => $sender,
                    'number'  => $phone,
                    'message' => $message,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim WhatsApp ke ' . $phone . ': ' . $e->getMessage());
        }
    }
}

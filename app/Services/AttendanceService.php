<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    protected float $officeLatitude;

    protected float $officeLongitude;

    protected int $radiusMeters;

    public function __construct()
    {
        $this->officeLatitude = config('absensi.office.latitude', -6.2088);
        $this->officeLongitude = config('absensi.office.longitude', 106.8456);
        $this->radiusMeters = config('absensi.office.radius', 100);
    }

    public function checkIn(Employee $employee, array $data): Attendance
    {
        $today = now()->toDateString();

        $existing = $this->getTodayAttendance($employee);
        if ($existing && $existing->check_in) {
            throw new \RuntimeException('Anda sudah melakukan check-in hari ini.');
        }

        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;

        if ($latitude && $longitude) {
            $distance = $this->calculateDistance(
                $this->officeLatitude, $this->officeLongitude,
                (float) $latitude, (float) $longitude
            );

            if ($distance > $this->radiusMeters) {
                throw new \RuntimeException('Anda berada di luar radius ' . $this->radiusMeters . ' meter dari lokasi kantor.');
            }
        }

        $shift = $this->getEmployeeShift($employee, $today);
        $checkInTime = now();

        $statusData = $this->calculateAttendanceStatus($employee, $shift, $checkInTime);

        $photoPath = null;
        if (isset($data['photo']) && $data['photo']->isValid()) {
            $photoPath = $data['photo']->store('attendance/check-in', 'public');
        }

        $attendance = Attendance::create([
            'employee_id'       => $employee->id,
            'date'              => $today,
            'check_in'          => $checkInTime,
            'status'            => $statusData['status'],
            'check_in_latitude'  => $latitude,
            'check_in_longitude' => $longitude,
            'check_in_photo'    => $photoPath,
            'check_in_note'     => $data['note'] ?? null,
            'check_in_ip'       => request()->ip(),
            'check_in_device'   => request()->userAgent(),
            'late_minutes'      => $statusData['late_minutes'],
            'is_manual'         => false,
        ]);

        Cache::forget('attendance_today_' . $employee->id);

        return $attendance;
    }

    public function checkOut(Employee $employee, array $data): Attendance
    {
        $today = now()->toDateString();

        $attendance = $this->getTodayAttendance($employee);
        if (!$attendance) {
            throw new \RuntimeException('Anda belum melakukan check-in hari ini.');
        }

        if ($attendance->check_out) {
            throw new \RuntimeException('Anda sudah melakukan check-out hari ini.');
        }

        $checkOutTime = now();
        $checkInTime = Carbon::parse($attendance->check_in);

        $workDuration = $checkOutTime->diffInSeconds($checkInTime);

        $shift = $this->getEmployeeShift($employee, $today);
        $overtimeMinutes = $this->calculateOvertimeMinutes($employee, $shift, $checkInTime, $checkOutTime);

        $photoPath = $attendance->check_out_photo;
        if (isset($data['photo']) && $data['photo']->isValid()) {
            $photoPath = $data['photo']->store('attendance/check-out', 'public');
        }

        $attendance->update([
            'check_out'          => $checkOutTime,
            'check_out_latitude'  => $data['latitude'] ?? $attendance->check_in_latitude,
            'check_out_longitude' => $data['longitude'] ?? $attendance->check_in_longitude,
            'check_out_photo'    => $photoPath,
            'check_out_note'     => $data['note'] ?? null,
            'check_out_ip'       => request()->ip(),
            'check_out_device'   => request()->userAgent(),
            'work_duration'      => $workDuration,
            'overtime_minutes'   => $overtimeMinutes,
        ]);

        Cache::forget('attendance_today_' . $employee->id);

        return $attendance->fresh();
    }

    public function calculateAttendanceStatus(Employee $employee, ?Shift $shift, Carbon $checkInTime): array
    {
        $status = AttendanceStatus::Hadir->value;
        $lateMinutes = 0;

        if ($shift && $shift->start_time) {
            $shiftStart = Carbon::parse($shift->start_time->format('H:i:s'));
            $tolerance = $shift->tolerance_minutes ?? 0;

            $graceEnd = $shiftStart->copy()->addMinutes($tolerance);

            if ($checkInTime->format('H:i:s') > $graceEnd->format('H:i:s')) {
                $status = AttendanceStatus::Telat->value;
                $lateMinutes = $shiftStart->diffInMinutes($checkInTime) - $tolerance;
                if ($lateMinutes < 0) {
                    $lateMinutes = 0;
                }
            }
        }

        return [
            'status'       => $status,
            'late_minutes' => $lateMinutes,
        ];
    }

    public function getTodayAttendance(Employee $employee): ?Attendance
    {
        return Cache::remember('attendance_today_' . $employee->id, 60, function () use ($employee) {
            return Attendance::where('employee_id', $employee->id)
                ->whereDate('date', now()->toDateString())
                ->first();
        });
    }

    public function getEmployeeShift(Employee $employee, string $date): ?Shift
    {
        $schedule = ShiftSchedule::where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->first();

        if ($schedule) {
            return $schedule->shift;
        }

        if ($employee->shift) {
            return $employee->shift;
        }

        return null;
    }

    protected function calculateOvertimeMinutes(Employee $employee, ?Shift $shift, Carbon $checkIn, Carbon $checkOut): int
    {
        if (!$shift || !$shift->end_time) {
            return 0;
        }

        $shiftEnd = Carbon::parse($shift->end_time->format('H:i:s'));

        if ($checkOut->format('H:i:s') > $shiftEnd->format('H:i:s')) {
            return $shiftEnd->diffInMinutes($checkOut);
        }

        return 0;
    }

    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

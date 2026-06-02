<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\EmployeeShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $today = now();
        $attendanceData = [];

        foreach ($employees as $employee) {
            $shiftId = $employee->shift_id ?? 1;
            $shift = Shift::find($shiftId);

            if (!$shift) {
                $shift = Shift::find(1);
            }

            for ($day = 29; $day >= 0; $day--) {
                $date = $today->copy()->subDays($day);
                $dayOfWeek = $date->dayOfWeek;

                $isWeekend = ($dayOfWeek === 0 || $dayOfWeek === 6);

                $rand = mt_rand(1, 100);

                if ($isWeekend) {
                    if ($rand <= 95) {
                        $status = 'alpha';
                        $attendanceData[] = $this->buildAlphaRecord($employee->id, $date);
                        continue;
                    }
                }

                if ($rand <= 80) {
                    $status = 'hadir';
                } elseif ($rand <= 85) {
                    $status = 'telat';
                } elseif ($rand <= 90) {
                    $status = 'izin';
                } elseif ($rand <= 93) {
                    $status = 'sakit';
                } elseif ($rand <= 95) {
                    $status = 'cuti';
                } else {
                    $status = 'alpha';
                }

                if ($status === 'alpha') {
                    $attendanceData[] = $this->buildAlphaRecord($employee->id, $date);
                    continue;
                }

                $checkIn = null;
                $checkOut = null;
                $workDuration = null;
                $lateMinutes = 0;
                $earlyLeaveMinutes = 0;
                $checkInLat = null;
                $checkInLng = null;

                $startTime = strtotime($shift->start_time);
                $endTime = strtotime($shift->end_time);
                $toleranceMinutes = $shift->tolerance_minutes ?? 15;

                if ($status === 'hadir' || $status === 'telat') {
                    if ($status === 'hadir') {
                        $checkInSeconds = $startTime + mt_rand(-5, $toleranceMinutes) * 60;
                        $lateMinutes = max(0, intdiv($checkInSeconds - $startTime, 60) - $toleranceMinutes);
                    } else {
                        $checkInSeconds = $startTime + $toleranceMinutes * 60 + mt_rand(1, 90) * 60;
                        $lateMinutes = intdiv($checkInSeconds - $startTime, 60) - $toleranceMinutes;
                    }

                    $checkIn = $date->format('Y-m-d') . ' ' . date('H:i:s', max(0, $checkInSeconds));
                    $checkInLat = -6.2 - mt_rand(0, 3000) / 10000;
                    $checkInLng = 106.8 + mt_rand(0, 2000) / 10000;

                    if ($endTime < $startTime) {
                        $adjustedEnd = $endTime + 86400;
                        $adjustedStart = $checkInSeconds;
                        if ($adjustedStart < $startTime) {
                            $adjustedStart += 86400;
                        }
                        $duration = $adjustedEnd - $adjustedStart;
                    } else {
                        $duration = $endTime - $checkInSeconds;
                    }

                    $duration = max($duration, 0);
                    $earlyLeaveMin = mt_rand(0, 30);
                    $duration -= $earlyLeaveMin * 60;
                    $earlyLeaveMinutes = $earlyLeaveMin;

                    $checkOutSeconds = $checkInSeconds + $duration;
                    $checkOut = $date->format('Y-m-d') . ' ' . date('H:i:s', $checkOutSeconds % 86400);
                    $workDuration = max(0, $duration);
                }

                $latitude = $checkInLat ?? (-6.2 - mt_rand(0, 3000) / 10000);
                $longitude = $checkInLng ?? (106.8 + mt_rand(0, 2000) / 10000);

                $attendanceData[] = [
                    'employee_id' => $employee->id,
                    'date' => $date->format('Y-m-d'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
                    'check_in_latitude' => $latitude,
                    'check_in_longitude' => $longitude,
                    'work_duration' => $workDuration ?? 0,
                    'late_minutes' => $lateMinutes ?? 0,
                    'early_leave_minutes' => $earlyLeaveMinutes ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($attendanceData) >= 200) {
                    DB::table('attendances')->insert($attendanceData);
                    $attendanceData = [];
                }
            }
        }

        if (count($attendanceData) > 0) {
            DB::table('attendances')->insert($attendanceData);
        }
    }

    private function buildAlphaRecord(int $employeeId, $date): array
    {
        return [
            'employee_id' => $employeeId,
            'date' => $date->format('Y-m-d'),
            'check_in' => null,
            'check_out' => null,
            'status' => 'alpha',
            'check_in_latitude' => null,
            'check_in_longitude' => null,
            'work_duration' => 0,
            'late_minutes' => 0,
            'early_leave_minutes' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

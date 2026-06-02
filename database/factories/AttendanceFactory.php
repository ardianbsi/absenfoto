<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $statuses = ['hadir', 'telat', 'izin', 'sakit', 'cuti', 'alpha'];
        $status = fake()->randomElement($statuses);

        $checkIn = null;
        $checkOut = null;
        $workDuration = null;
        $lateMinutes = 0;
        $earlyLeaveMinutes = 0;

        $date = fake()->dateTimeBetween('-30 days', 'today');
        $dateStr = $date->format('Y-m-d');

        if (in_array($status, ['hadir', 'telat'])) {
            $baseHour = fake()->numberBetween(6, 7);
            $baseMinute = fake()->numberBetween(0, 59);
            $checkInStr = sprintf('%02d:%02d:00', $baseHour, $baseMinute);
            $checkIn = $dateStr . ' ' . $checkInStr;

            $outHour = fake()->numberBetween(15, 17);
            $outMinute = fake()->numberBetween(0, 59);
            $checkOutStr = sprintf('%02d:%02d:00', $outHour, $outMinute);
            $checkOut = $dateStr . ' ' . $checkOutStr;

            $ci = strtotime($checkIn);
            $co = strtotime($checkOut);
            $workDuration = ($co - $ci) / 60;

            if ($status === 'telat') {
                $lateMinutes = fake()->numberBetween(16, 90);
            } else {
                $lateMinutes = fake()->numberBetween(0, 10);
            }

            $earlyLeaveMinutes = fake()->numberBetween(0, 30);
        }

        return [
            'date' => $dateStr,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => $status,
            'check_in_latitude' => fake()->latitude(-6.5, -6.1),
            'check_in_longitude' => fake()->longitude(106.5, 107.0),
            'work_duration' => $workDuration,
            'late_minutes' => $lateMinutes,
            'early_leave_minutes' => $earlyLeaveMinutes,
        ];
    }
}

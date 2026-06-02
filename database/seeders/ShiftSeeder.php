<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\Employee;
use App\Models\EmployeeShift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Pagi',
                'code' => 'PAGI',
                'type' => 'fixed',
                'color' => '#28a745',
                'start_time' => '07:00:00',
                'end_time' => '15:00:00',
                'tolerance_minutes' => 15,
                'max_late_minutes' => 120,
                'is_active' => true,
                'description' => 'Shift Pagi (07:00 - 15:00)',
            ],
            [
                'name' => 'Siang',
                'code' => 'SIANG',
                'type' => 'fixed',
                'color' => '#007bff',
                'start_time' => '13:00:00',
                'end_time' => '21:00:00',
                'tolerance_minutes' => 15,
                'max_late_minutes' => 120,
                'is_active' => true,
                'description' => 'Shift Siang (13:00 - 21:00)',
            ],
            [
                'name' => 'Malam',
                'code' => 'MALAM',
                'type' => 'fixed',
                'color' => '#6f42c1',
                'start_time' => '21:00:00',
                'end_time' => '05:00:00',
                'tolerance_minutes' => 15,
                'max_late_minutes' => 120,
                'is_active' => true,
                'description' => 'Shift Malam (21:00 - 05:00)',
            ],
            [
                'name' => 'Flexible',
                'code' => 'FLEX',
                'type' => 'flexible',
                'color' => '#fd7e14',
                'start_time' => '00:00:00',
                'end_time' => '23:59:00',
                'tolerance_minutes' => 15,
                'max_late_minutes' => 120,
                'is_active' => true,
                'description' => 'Shift Flexible (00:00 - 23:59)',
            ],
        ];

        foreach ($shifts as $data) {
            Shift::create($data);
        }

        $employees = Employee::all();
        $shiftIds = [1, 2, 4];
        $today = now()->toDateString();

        foreach ($employees as $employee) {
            $shiftId = $shiftIds[($employee->id - 1) % count($shiftIds)];

            EmployeeShift::create([
                'employee_id' => $employee->id,
                'shift_id' => $shiftId,
                'start_date' => $today,
                'end_date' => null,
                'is_active' => true,
            ]);

            $employee->shift_id = $shiftId;
            $employee->save();
        }
    }
}

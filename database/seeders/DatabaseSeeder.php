<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            DepartmentAndPositionSeeder::class,
            UserSeeder::class,
            EmployeeSeeder::class,
            ShiftSeeder::class,
            AttendanceSeeder::class,
            LeaveAndOvertimeSeeder::class,
            HolidaySeeder::class,
        ]);
    }
}

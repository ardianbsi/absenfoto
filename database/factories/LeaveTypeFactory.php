<?php

namespace Database\Factories;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    public function definition(): array
    {
        $types = [
            ['name' => 'Cuti Tahunan', 'code' => 'CT', 'quota' => 12, 'is_paid' => true, 'is_deduct_quota' => true, 'require_attachment' => false, 'max_days' => 30],
            ['name' => 'Sakit', 'code' => 'SK', 'quota' => 999, 'is_paid' => true, 'is_deduct_quota' => false, 'require_attachment' => true, 'max_days' => 999],
            ['name' => 'Izin Pribadi', 'code' => 'IP', 'quota' => 5, 'is_paid' => false, 'is_deduct_quota' => true, 'require_attachment' => false, 'max_days' => 3],
            ['name' => 'Work From Home', 'code' => 'WFH', 'quota' => 5, 'is_paid' => true, 'is_deduct_quota' => true, 'require_attachment' => false, 'max_days' => 999],
            ['name' => 'Dinas Luar', 'code' => 'DL', 'quota' => 999, 'is_paid' => true, 'is_deduct_quota' => false, 'require_attachment' => false, 'max_days' => 999],
        ];

        $type = fake()->unique()->randomElement($types);

        return $type;
    }
}

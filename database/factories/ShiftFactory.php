<?php

namespace Database\Factories;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
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
            ],
        ];

        $shift = fake()->unique()->randomElement($shifts);

        return $shift;
    }
}

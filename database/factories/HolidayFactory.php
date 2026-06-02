<?php

namespace Database\Factories;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Factories\Factory;

class HolidayFactory extends Factory
{
    protected $model = Holiday::class;

    public function definition(): array
    {
        $holidays = [
            ['name' => 'Tahun Baru Masehi', 'type' => 'national', 'is_recurring' => true],
            ['name' => 'Hari Buruh Internasional', 'type' => 'national', 'is_recurring' => true],
            ['name' => 'Hari Kebangkitan Nasional', 'type' => 'national', 'is_recurring' => true],
            ['name' => 'Hari Kemerdekaan RI', 'type' => 'national', 'is_recurring' => true],
            ['name' => 'Hari Pahlawan', 'type' => 'national', 'is_recurring' => true],
            ['name' => 'Hari Natal', 'type' => 'national', 'is_recurring' => true],
            ['name' => 'Tahun Baru Islam', 'type' => 'national', 'is_recurring' => true],
            ['name' => 'Isra Miraj', 'type' => 'national', 'is_recurring' => true],
        ];

        $holiday = fake()->unique()->randomElement($holidays);

        return [
            'name' => $holiday['name'],
            'date' => fake()->dateTimeBetween('2026-01-01', '2026-12-31')->format('Y-m-d'),
            'type' => $holiday['type'],
            'year' => 2026,
            'is_recurring' => $holiday['is_recurring'],
        ];
    }
}

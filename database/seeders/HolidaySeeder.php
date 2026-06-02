<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            ['name' => 'Tahun Baru 2026 Masehi', 'date' => '2026-01-01', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Tahun Baru Masehi'],
            ['name' => 'Isra Miraj Nabi Muhammad SAW', 'date' => '2026-01-29', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Isra Miraj 1447 H'],
            ['name' => 'Tahun Baru Imlek 2577', 'date' => '2026-02-17', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Tahun Baru Imlek Kongzili'],
            ['name' => 'Hari Suci Nyepi Tahun Baru Saka 1948', 'date' => '2026-03-29', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Hari Raya Nyepi'],
            ['name' => 'Wafat Isa Almasih', 'date' => '2026-04-03', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Good Friday'],
            ['name' => 'Hari Raya Idul Fitri 1447 H', 'date' => '2026-03-31', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Hari Raya Idul Fitri Hari ke-1'],
            ['name' => 'Hari Raya Idul Fitri 1447 H', 'date' => '2026-04-01', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Hari Raya Idul Fitri Hari ke-2'],
            ['name' => 'Hari Buruh Internasional', 'date' => '2026-05-01', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Labour Day'],
            ['name' => 'Kenaikan Isa Almasih', 'date' => '2026-05-21', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Ascension Day'],
            ['name' => 'Hari Raya Idul Adha 1447 H', 'date' => '2026-05-27', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Hari Raya Idul Adha'],
            ['name' => 'Hari Lahir Pancasila', 'date' => '2026-06-01', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Hari Lahir Pancasila'],
            ['name' => 'Hari Kemerdekaan Republik Indonesia', 'date' => '2026-08-17', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'HUT RI Ke-81'],
            ['name' => 'Hari Raya Natal', 'date' => '2026-12-25', 'type' => 'national', 'year' => 2026, 'is_recurring' => true, 'description' => 'Hari Raya Natal'],
        ];

        foreach ($holidays as $data) {
            Holiday::create($data);
        }
    }
}

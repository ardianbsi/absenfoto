<?php

namespace Database\Factories;

use App\Models\OvertimeRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class OvertimeRequestFactory extends Factory
{
    protected $model = OvertimeRequest::class;

    private static array $descriptions = [
        'Menyelesaikan laporan bulanan yang belum selesai',
        'Meeting deadline project dengan client',
        'Persiapan presentasi untuk meeting besok',
        'Menyelesaikan bug fixing aplikasi',
        'Penginputan data inventaris perusahaan',
        'Persiapan audit keuangan akhir bulan',
        'Menyelesaikan laporan keuangan sebelum deadline',
        'Meeting koordinasi dengan tim marketing',
        'Pengembangan fitur baru aplikasi',
        'Revisi dokumen tender proyek',
        'Persiapan event perusahaan',
        'Menyelesaikan backup data server',
        'Rollout update sistem ke seluruh cabang',
        'Monitoring sistem selama maintenance',
        'Persiapan laporan tahunan',
    ];

    public function definition(): array
    {
        $startHour = fake()->numberBetween(16, 19);
        $startMinute = fake()->randomElement([0, 15, 30, 45]);
        $duration = fake()->randomElement([60, 90, 120, 150, 180, 240]);
        $startTime = sprintf('%02d:%02d:00', $startHour, $startMinute);
        $startTs = strtotime($startTime);
        $endTs = $startTs + ($duration * 60);
        $endTime = date('H:i:s', $endTs);

        return [
            'date' => fake()->dateTimeBetween('-30 days', '+7 days')->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_minutes' => $duration,
            'description' => fake()->randomElement(self::$descriptions),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'attachment' => null,
        ];
    }
}

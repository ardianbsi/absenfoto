<?php

namespace Database\Factories;

use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    private static array $reasons = [
        'Acara keluarga (pernikahan saudara)',
        'Sakit demam tinggi dan tidak bisa beraktivitas',
        'Izin pribadi mengurus administrasi bank',
        'Hari raya keagamaan',
        'Menjenguk orang tua yang sakit di kampung',
        'Ada acara khitanan anak',
        'Mengantar istri melahirkan',
        'Ada musibah di keluarga (kemalangan)',
        'Ibadah keagamaan tahunan',
        'Ada urusan mendadak di rumah',
        'Anak sakit dan perlu pengawasan',
        'Pindahan rumah',
        'Mengurus perpanjangan KTP/paspor',
        'Ada acara adat keluarga',
        'Konsultasi kesehatan di rumah sakit',
    ];

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-60 days', '+30 days');
        $totalDays = fake()->numberBetween(1, 5);
        $endDate = (clone $startDate)->modify('+' . ($totalDays - 1) . ' days');

        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_days' => $totalDays,
            'reason' => fake()->randomElement(self::$reasons),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'attachment' => null,
        ];
    }
}

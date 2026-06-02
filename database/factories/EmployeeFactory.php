<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    private static array $firstNames = [
        'Agus', 'Ahmad', 'Bambang', 'Budi', 'Dewi', 'Dian', 'Eko', 'Fitri',
        'Gunawan', 'Hendra', 'Hesti', 'Indah', 'Indra', 'Joko', 'Kurniawan',
        'Lestari', 'Made', 'Nurul', 'Oka', 'Putri', 'Ratna', 'Rudi', 'Sari',
        'Slamet', 'Tri', 'Utami', 'Wahyu', 'Widi', 'Yanti', 'Zainal',
        'Bagus', 'Cici', 'Deni', 'Fajar', 'Gilang', 'Herman', 'Intan',
        'Kadek', 'Linda', 'Mega', 'Nina', 'Olivia', 'Pramono', 'Rina',
    ];

    private static array $lastNames = [
        'Pratama', 'Wijaya', 'Saputra', 'Santoso', 'Hidayat', 'Nugroho',
        'Susanto', 'Hermawan', 'Wibowo', 'Rahayu', 'Handayani', 'Lestari',
        'Utami', 'Wulandari', 'Hartati', 'Purwanti', 'Kusuma', 'Setiawan',
        'Purnomo', 'Maulana', 'Nasution', 'Siregar', 'Sinaga', 'Ginting',
        'Sembiring', 'Tamba', 'Manurung', 'Siahaan', 'Simanjuntak', 'Sitompul',
    ];

    private static array $cities = [
        'Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta', 'Medan',
        'Makassar', 'Denpasar', 'Palembang', 'Malang', 'Bogor', 'Tangerang',
        'Bekasi', 'Depok', 'Surakarta', 'Manado', 'Pekanbaru', 'Banjarmasin',
        'Pontianak', 'Balikpapan', 'Samarinda', 'Padang', 'Jambi', 'Aceh',
        'Mataram', 'Kupang', 'Ambon', 'Jayapura', 'Ternate', 'Gorontalo',
    ];

    private static array $streets = [
        'Merdeka', 'Sudirman', 'Thamrin', 'Gatot Subroto', 'Rasuna Said',
        'Kuningan', 'Pahlawan', 'Diponegoro', 'A. Yani', 'S. Parman',
        'M.T. Haryono', 'Pattimura', 'Hasanuddin', 'Sisingamangaraja',
        'Wolter Monginsidi', 'Cendrawasih', 'Anggrek', 'Mawar', 'Melati',
        'Kartini', 'Imam Bonjol', 'Pemuda', 'Veteran', 'Padjajaran',
    ];

    public function definition(): array
    {
        $firstName = fake()->randomElement(self::$firstNames);
        $lastName = fake()->randomElement(self::$lastNames);
        $fullName = $firstName . ' ' . $lastName;
        $gender = fake()->randomElement(['Laki-laki', 'Perempuan']);
        $city = fake()->randomElement(self::$cities);

        return [
            'nik' => fake()->unique()->numerify('################'),
            'full_name' => $fullName,
            'phone' => '08' . fake()->numerify('##########'),
            'address' => 'Jl. ' . fake()->randomElement(self::$streets) . ' No. '
                . fake()->numberBetween(1, 200) . ', RT '
                . fake()->numberBetween(1, 15) . '/RW '
                . fake()->numberBetween(1, 10) . ', Kel. '
                . fake()->word() . ', Kec. ' . fake()->word()
                . ', ' . $city,
            'place_of_birth' => $city,
            'date_of_birth' => fake()->dateTimeBetween('-55 years', '-20 years')->format('Y-m-d'),
            'gender' => $gender,
            'religion' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu']),
            'marital_status' => fake()->randomElement(['Belum Menikah', 'Menikah', 'Cerai']),
            'join_date' => fake()->dateTimeBetween('-10 years', '-1 month')->format('Y-m-d'),
            'work_status' => fake()->randomElement(['Permanent', 'Contract', 'Intern', 'Probation']),
            'is_active' => true,
        ];
    }
}

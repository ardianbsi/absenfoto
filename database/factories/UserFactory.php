<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    private static array $usedNames = [];

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

    public function definition(): array
    {
        $firstName = fake()->randomElement(self::$firstNames);
        $lastName = fake()->randomElement(self::$lastNames);
        $fullName = $firstName . ' ' . $lastName;
        $username = Str::slug($firstName . '.' . $lastName, '.');

        return [
            'name' => $fullName,
            'username' => fake()->unique()->regexify('[a-z]{3,8}\.[a-z]{3,8}'),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'is_active' => true,
            'theme_preference' => 'light',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

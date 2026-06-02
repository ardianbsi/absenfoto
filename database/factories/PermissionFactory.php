<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'display_name' => fake()->words(2, true),
            'guard_name' => 'web',
            'module' => fake()->randomElement(['employee', 'attendance', 'leave', 'overtime', 'payroll', 'report', 'setting']),
        ];
    }
}

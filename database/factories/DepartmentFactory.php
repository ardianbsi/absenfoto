<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $departments = [
            'IT' => 'IT-001',
            'Human Resources' => 'HR-001',
            'Finance' => 'FIN-001',
            'Marketing' => 'MKT-001',
            'Operations' => 'OPS-001',
            'Legal' => 'LGL-001',
            'Research & Development' => 'RND-001',
        ];

        $name = fake()->unique()->randomElement(array_keys($departments));

        return [
            'name' => $name,
            'code' => $departments[$name],
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}

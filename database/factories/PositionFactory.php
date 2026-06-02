<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        $positions = [
            'Manager IT', 'Staff IT', 'Programmer', 'System Analyst', 'Database Administrator',
            'Manager HR', 'HR Staff', 'Recruitment Specialist', 'Training Coordinator',
            'Manager Finance', 'Staff Finance', 'Accountant', 'Tax Specialist',
            'Manager Marketing', 'Staff Marketing', 'Brand Specialist', 'Digital Marketing',
            'Manager Operations', 'Staff Operations', 'Logistic Coordinator',
            'Manager Legal', 'Legal Staff', 'Legal Counsel',
            'Manager R&D', 'R&D Staff', 'Product Developer',
        ];

        return [
            'name' => fake()->unique()->randomElement($positions),
            'department_id' => fake()->numberBetween(1, 7),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}

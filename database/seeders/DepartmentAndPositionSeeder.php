<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class DepartmentAndPositionSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'IT', 'code' => 'IT-001', 'description' => 'Information Technology Department', 'is_active' => true],
            ['name' => 'Human Resources', 'code' => 'HR-001', 'description' => 'Human Resources Department', 'is_active' => true],
            ['name' => 'Finance', 'code' => 'FIN-001', 'description' => 'Finance Department', 'is_active' => true],
            ['name' => 'Marketing', 'code' => 'MKT-001', 'description' => 'Marketing Department', 'is_active' => true],
            ['name' => 'Operations', 'code' => 'OPS-001', 'description' => 'Operations Department', 'is_active' => true],
            ['name' => 'Legal', 'code' => 'LGL-001', 'description' => 'Legal Department', 'is_active' => true],
            ['name' => 'Research & Development', 'code' => 'RND-001', 'description' => 'Research & Development Department', 'is_active' => true],
        ];

        foreach ($departments as $data) {
            Department::create($data);
        }

        $positions = [
            ['name' => 'Manager IT', 'department_id' => 1, 'description' => 'IT Department Manager', 'is_active' => true],
            ['name' => 'Staff IT', 'department_id' => 1, 'description' => 'IT Staff', 'is_active' => true],
            ['name' => 'Programmer', 'department_id' => 1, 'description' => 'Software Programmer', 'is_active' => true],
            ['name' => 'System Analyst', 'department_id' => 1, 'description' => 'System Analyst', 'is_active' => true],
            ['name' => 'Database Administrator', 'department_id' => 1, 'description' => 'Database Administrator', 'is_active' => true],

            ['name' => 'Manager HR', 'department_id' => 2, 'description' => 'HR Department Manager', 'is_active' => true],
            ['name' => 'HR Staff', 'department_id' => 2, 'description' => 'Human Resources Staff', 'is_active' => true],
            ['name' => 'Recruitment Specialist', 'department_id' => 2, 'description' => 'Recruitment Specialist', 'is_active' => true],
            ['name' => 'Training Coordinator', 'department_id' => 2, 'description' => 'Training Coordinator', 'is_active' => true],

            ['name' => 'Manager Finance', 'department_id' => 3, 'description' => 'Finance Department Manager', 'is_active' => true],
            ['name' => 'Staff Finance', 'department_id' => 3, 'description' => 'Finance Staff', 'is_active' => true],
            ['name' => 'Accountant', 'department_id' => 3, 'description' => 'Accountant', 'is_active' => true],
            ['name' => 'Tax Specialist', 'department_id' => 3, 'description' => 'Tax Specialist', 'is_active' => true],

            ['name' => 'Manager Marketing', 'department_id' => 4, 'description' => 'Marketing Department Manager', 'is_active' => true],
            ['name' => 'Staff Marketing', 'department_id' => 4, 'description' => 'Marketing Staff', 'is_active' => true],
            ['name' => 'Brand Specialist', 'department_id' => 4, 'description' => 'Brand Specialist', 'is_active' => true],
            ['name' => 'Digital Marketing', 'department_id' => 4, 'description' => 'Digital Marketing Specialist', 'is_active' => true],

            ['name' => 'Manager Operations', 'department_id' => 5, 'description' => 'Operations Department Manager', 'is_active' => true],
            ['name' => 'Staff Operations', 'department_id' => 5, 'description' => 'Operations Staff', 'is_active' => true],
            ['name' => 'Logistic Coordinator', 'department_id' => 5, 'description' => 'Logistic Coordinator', 'is_active' => true],

            ['name' => 'Manager Legal', 'department_id' => 6, 'description' => 'Legal Department Manager', 'is_active' => true],
            ['name' => 'Legal Staff', 'department_id' => 6, 'description' => 'Legal Staff', 'is_active' => true],
            ['name' => 'Legal Counsel', 'department_id' => 6, 'description' => 'Legal Counsel', 'is_active' => true],

            ['name' => 'Manager R&D', 'department_id' => 7, 'description' => 'R&D Department Manager', 'is_active' => true],
            ['name' => 'R&D Staff', 'department_id' => 7, 'description' => 'R&D Staff', 'is_active' => true],
            ['name' => 'Product Developer', 'department_id' => 7, 'description' => 'Product Developer', 'is_active' => true],
        ];

        foreach ($positions as $data) {
            Position::create($data);
        }
    }
}

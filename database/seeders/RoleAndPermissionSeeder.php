<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Admin', 'guard_name' => 'web', 'is_system' => true],
            ['name' => 'hr', 'display_name' => 'HR', 'guard_name' => 'web', 'is_system' => true],
            ['name' => 'manager', 'display_name' => 'Manager/Supervisor', 'guard_name' => 'web', 'is_system' => true],
            ['name' => 'employee', 'display_name' => 'Employee/Karyawan', 'guard_name' => 'web', 'is_system' => true],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $permissions = [
            ['name' => 'manage-employees', 'display_name' => 'Manage Employees', 'guard_name' => 'web', 'module' => 'employee'],
            ['name' => 'manage-shifts', 'display_name' => 'Manage Shifts', 'guard_name' => 'web', 'module' => 'attendance'],
            ['name' => 'manage-attendance', 'display_name' => 'Manage Attendance', 'guard_name' => 'web', 'module' => 'attendance'],
            ['name' => 'manage-leave', 'display_name' => 'Manage Leave', 'guard_name' => 'web', 'module' => 'leave'],
            ['name' => 'manage-overtime', 'display_name' => 'Manage Overtime', 'guard_name' => 'web', 'module' => 'overtime'],
            ['name' => 'manage-departments', 'display_name' => 'Manage Departments', 'guard_name' => 'web', 'module' => 'employee'],
            ['name' => 'manage-positions', 'display_name' => 'Manage Positions', 'guard_name' => 'web', 'module' => 'employee'],
            ['name' => 'manage-holidays', 'display_name' => 'Manage Holidays', 'guard_name' => 'web', 'module' => 'attendance'],
            ['name' => 'manage-announcements', 'display_name' => 'Manage Announcements', 'guard_name' => 'web', 'module' => 'general'],
            ['name' => 'manage-reports', 'display_name' => 'Manage Reports', 'guard_name' => 'web', 'module' => 'report'],
            ['name' => 'manage-settings', 'display_name' => 'Manage Settings', 'guard_name' => 'web', 'module' => 'setting'],
            ['name' => 'view-dashboard', 'display_name' => 'View Dashboard', 'guard_name' => 'web', 'module' => 'general'],
            ['name' => 'view-attendance', 'display_name' => 'View Personal Attendance', 'guard_name' => 'web', 'module' => 'attendance'],
            ['name' => 'view-leave', 'display_name' => 'View Personal Leave', 'guard_name' => 'web', 'module' => 'leave'],
            ['name' => 'view-overtime', 'display_name' => 'View Personal Overtime', 'guard_name' => 'web', 'module' => 'overtime'],
            ['name' => 'submit-leave', 'display_name' => 'Submit Leave Request', 'guard_name' => 'web', 'module' => 'leave'],
            ['name' => 'submit-overtime', 'display_name' => 'Submit Overtime Request', 'guard_name' => 'web', 'module' => 'overtime'],
            ['name' => 'approve-leave', 'display_name' => 'Approve Leave Request', 'guard_name' => 'web', 'module' => 'leave'],
            ['name' => 'approve-overtime', 'display_name' => 'Approve Overtime Request', 'guard_name' => 'web', 'module' => 'overtime'],
            ['name' => 'manage-payroll', 'display_name' => 'Manage Payroll', 'guard_name' => 'web', 'module' => 'payroll'],
            ['name' => 'view-reports', 'display_name' => 'View Reports', 'guard_name' => 'web', 'module' => 'report'],
        ];

        foreach ($permissions as $perm) {
            Permission::create($perm);
        }

        $superAdmin = Role::where('name', 'super_admin')->first();
        $hr = Role::where('name', 'hr')->first();
        $manager = Role::where('name', 'manager')->first();
        $employee = Role::where('name', 'employee')->first();

        $superAdmin->permissions()->attach(Permission::pluck('id'));

        $hrPermissions = Permission::whereIn('name', [
            'manage-employees', 'manage-shifts', 'manage-attendance', 'manage-leave',
            'manage-overtime', 'manage-departments', 'manage-positions', 'manage-holidays',
            'manage-announcements', 'manage-reports', 'view-dashboard', 'view-attendance',
            'approve-leave', 'approve-overtime', 'manage-payroll', 'view-reports', 'manage-settings',
        ])->pluck('id');
        $hr->permissions()->attach($hrPermissions);

        $managerPermissions = Permission::whereIn('name', [
            'view-dashboard', 'view-attendance', 'view-leave', 'view-overtime', 'view-reports',
        ])->pluck('id');
        $manager->permissions()->attach($managerPermissions);

        $employeePermissions = Permission::whereIn('name', [
            'view-dashboard', 'view-attendance', 'view-leave', 'view-overtime',
            'submit-leave', 'submit-overtime',
        ])->pluck('id');
        $employee->permissions()->attach($employeePermissions);
    }
}

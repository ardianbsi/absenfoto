<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case HR = 'hr';
    case Manager = 'manager';
    case Employee = 'employee';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::HR => 'HR',
            self::Manager => 'Manager/Supervisor',
            self::Employee => 'Employee',
        };
    }
}

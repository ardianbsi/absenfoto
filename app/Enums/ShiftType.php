<?php

namespace App\Enums;

enum ShiftType: string
{
    case Fixed = 'fixed';
    case Flexible = 'flexible';
    case Rotating = 'rotating';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fixed',
            self::Flexible => 'Flexible',
            self::Rotating => 'Rotating',
        };
    }
}

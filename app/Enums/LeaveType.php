<?php

namespace App\Enums;

enum LeaveType: string
{
    case CutiTahunan = 'cuti_tahunan';
    case Sakit = 'sakit';
    case IzinPribadi = 'izin_pribadi';
    case WFH = 'wfh';
    case DinasLuar = 'dinas_luar';

    public function label(): string
    {
        return match ($this) {
            self::CutiTahunan => 'Cuti Tahunan',
            self::Sakit => 'Sakit',
            self::IzinPribadi => 'Izin Pribadi',
            self::WFH => 'Work From Home',
            self::DinasLuar => 'Dinas Luar',
        };
    }

    public function requiresApproval(): bool
    {
        return match ($this) {
            self::CutiTahunan, self::IzinPribadi, self::DinasLuar => true,
            self::Sakit, self::WFH => false,
        };
    }

    public function maxDays(): ?int
    {
        return match ($this) {
            self::CutiTahunan => 12,
            self::Sakit => null,
            self::IzinPribadi => 2,
            self::WFH => 30,
            self::DinasLuar => null,
        };
    }
}

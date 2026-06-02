<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Hadir = 'hadir';
    case Telat = 'telat';
    case Izin = 'izin';
    case Sakit = 'sakit';
    case Cuti = 'cuti';
    case Alpha = 'alpha';
    case WFH = 'wfh';

    public function label(): string
    {
        return match ($this) {
            self::Hadir => 'Hadir',
            self::Telat => 'Telat',
            self::Izin => 'Izin',
            self::Sakit => 'Sakit',
            self::Cuti => 'Cuti',
            self::Alpha => 'Alpha',
            self::WFH => 'WFH',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Hadir => 'success',
            self::Telat => 'warning',
            self::Izin => 'info',
            self::Sakit => 'secondary',
            self::Cuti => 'primary',
            self::Alpha => 'danger',
            self::WFH => 'indigo',
        };
    }
}

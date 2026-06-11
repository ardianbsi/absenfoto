<?php

namespace App\Enums;

enum AttendanceType: string
{
    case WFO = 'wfo';
    case WAF = 'waf';
    case WFH = 'wfh';

    public function label(): string
    {
        return match ($this) {
            self::WFO => 'WFO',
            self::WAF => 'WAF',
            self::WFH => 'WFH',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::WFO => 'Work From Office',
            self::WAF => 'Work From Anywhere',
            self::WFH => 'Work From Home',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::WFO => 'blue',
            self::WAF => 'green',
            self::WFH => 'indigo',
        };
    }
}

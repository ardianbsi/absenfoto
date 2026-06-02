<?php

namespace App\Enums;

enum WorkStatus: string
{
    case Contract = 'contract';
    case Permanent = 'permanent';
    case Intern = 'intern';
    case Probation = 'probation';

    public function label(): string
    {
        return match ($this) {
            self::Contract => 'Contract',
            self::Permanent => 'Permanent',
            self::Intern => 'Intern',
            self::Probation => 'Probation',
        };
    }
}

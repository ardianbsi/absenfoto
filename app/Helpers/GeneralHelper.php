<?php

namespace App\Helpers;

use Carbon\Carbon;

class GeneralHelper
{
    public static function formatDate($date): string
    {
        if (!$date) return '-';

        $carbon = Carbon::parse($date);

        $day = self::getIndonesianDay($carbon->format('l'));
        $month = self::getIndonesianMonth((int)$carbon->format('n'));

        return $day . ', ' . $carbon->format('j') . ' ' . $month . ' ' . $carbon->format('Y');
    }

    public static function formatDateTime($date): string
    {
        if (!$date) return '-';

        $carbon = Carbon::parse($date);

        $day = self::getIndonesianDay($carbon->format('l'));
        $month = self::getIndonesianMonth((int)$carbon->format('n'));

        return $day . ', ' . $carbon->format('j') . ' ' . $month . ' ' . $carbon->format('Y') . ' ' . $carbon->format('H:i') . ' WIB';
    }

    public static function formatRupiah($amount): string
    {
        if (is_null($amount)) return '-';

        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }

    public static function getDaysLate($checkIn, $shiftStart, $tolerance = 0): int
    {
        if (!$checkIn || !$shiftStart) return 0;

        $checkInMinutes = self::timeToMinutes($checkIn);
        $shiftStartMinutes = self::timeToMinutes($shiftStart);

        $late = $checkInMinutes - $shiftStartMinutes - $tolerance;

        return max(0, $late);
    }

    public static function getIndonesianDay($day): string
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        return $days[$day] ?? $day;
    }

    public static function getIndonesianMonth($month): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $months[$month] ?? '-';
    }

    public static function timeToMinutes($time): int
    {
        if (!$time) return 0;

        $parts = explode(':', $time);

        $hours = (int)($parts[0] ?? 0);
        $minutes = (int)($parts[1] ?? 0);

        return ($hours * 60) + $minutes;
    }
}

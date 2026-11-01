<?php

namespace App\Support;

use Carbon\CarbonInterface;

class Jalali
{
    /**
     * Convert a Gregorian date to the Solar Hijri calendar.
     * Returns a string such as "۱۴ آبان ۱۴۰۵".
     */
    public static function date(CarbonInterface|string|null $date, bool $withTime = false): string
    {
        if (! $date) {
            return '';
        }

        $carbon = self::carbon($date);
        [$jy, $jm, $jd] = self::parts($carbon);

        $months = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];

        $text = self::digits((string) $jd).' '.$months[$jm - 1].' '.self::digits((string) $jy);

        if ($withTime) {
            $text .= '، '.self::digits($carbon->format('H:i'));
        }

        return $text;
    }

    /**
     * Solar Hijri year only, e.g. "۱۴۰۵".
     */
    public static function year(CarbonInterface|string|null $date): string
    {
        if (! $date) {
            return '';
        }

        return self::digits((string) self::parts(self::carbon($date))[0]);
    }

    private static function carbon(CarbonInterface|string $date): CarbonInterface
    {
        return $date instanceof CarbonInterface ? $date : \Illuminate\Support\Carbon::parse($date);
    }

    /**
     * @return array{0:int, 1:int, 2:int} [year, month, day]
     */
    private static function parts(CarbonInterface $carbon): array
    {
        [$gy, $gm, $gd] = [(int) $carbon->format('Y'), (int) $carbon->format('n'), (int) $carbon->format('j')];

        $gDaysBeforeMonth = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];

        $gy2 = $gm > 2 ? $gy + 1 : $gy;
        $days = 355666 + (365 * $gy) + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400) + $gd + $gDaysBeforeMonth[$gm - 1];

        $jy = -1595 + (33 * intdiv($days, 12053));
        $days %= 12053;

        $jy += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $jy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        if ($days < 186) {
            $jm = 1 + intdiv($days, 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + intdiv($days - 186, 30);
            $jd = 1 + (($days - 186) % 30);
        }

        return [$jy, $jm, $jd];
    }

    /**
     * Convert Latin digits to Persian digits.
     */
    public static function digits(?string $value): string
    {
        return str_replace(
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'],
            (string) $value
        );
    }
}

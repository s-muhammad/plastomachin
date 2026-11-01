<?php

use App\Models\Setting;
use App\Support\Jalali;

if (! function_exists('setting')) {
    /**
     * Read a site setting with a fallback default.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::values()[$key] ?? $default;
    }
}

if (! function_exists('fa_num')) {
    /**
     * Convert Latin digits to Persian digits for display.
     */
    function fa_num(mixed $value): string
    {
        return Jalali::digits((string) $value);
    }
}

if (! function_exists('jalali_date')) {
    /**
     * Format a date in the Solar Hijri calendar.
     */
    function jalali_date(mixed $date, bool $withTime = false): string
    {
        return Jalali::date($date, $withTime);
    }
}

<?php

namespace App\Support;

use App\Models\Setting;

class Site
{
    /**
     * Digits-only local phone number for tel: links.
     */
    public static function phoneHref(?string $phone = null): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone ?? Setting::get('phone', ''));

        return 'tel:'.$phone;
    }

    /**
     * wa.me link with an optional prefilled message.
     */
    public static function whatsappUrl(?string $number = null, ?string $text = null): string
    {
        $number = preg_replace('/[^0-9]/', '', $number ?? Setting::get('whatsapp', ''));
        $number = preg_replace('/^0/', '98', $number);

        $url = 'https://wa.me/'.$number;

        if ($text) {
            $url .= '?text='.rawurlencode($text);
        }

        return $url;
    }

    /**
     * Group a phone number for display without changing the digits.
     */
    public static function phoneDisplay(?string $phone = null): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone ?? Setting::get('phone', ''));

        if (strlen($phone) === 11) {
            return substr($phone, 0, 4).' '.substr($phone, 4, 3).' '.substr($phone, 7);
        }

        return $phone;
    }

    public static function digits(?string $value): string
    {
        return Jalali::digits($value);
    }
}

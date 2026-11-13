<?php

namespace App\Enums\SeoPlan;

enum SearchIntent: string
{
    case Commercial = 'commercial';
    case Informational = 'informational';
    case Comparison = 'comparison';
    case Local = 'local';
    case Brand = 'brand';

    public function label(): string
    {
        return match ($this) {
            self::Commercial => 'تجاری',
            self::Informational => 'اطلاعاتی',
            self::Comparison => 'مقایسه‌ای',
            self::Local => 'محلی',
            self::Brand => 'برند',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

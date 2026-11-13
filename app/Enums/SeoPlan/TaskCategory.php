<?php

namespace App\Enums\SeoPlan;

enum TaskCategory: string
{
    case Technical = 'technical';
    case OnPage = 'on_page';
    case Content = 'content';
    case Local = 'local';
    case Links = 'links';

    public function label(): string
    {
        return match ($this) {
            self::Technical => 'فنی',
            self::OnPage => 'سئوی صفحه',
            self::Content => 'محتوا',
            self::Local => 'سئوی محلی',
            self::Links => 'لینک‌سازی',
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

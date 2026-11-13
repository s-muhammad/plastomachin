<?php

namespace App\Enums\SeoPlan;

enum PageType: string
{
    case Home = 'home';
    case Service = 'service';
    case Support = 'support';
    case Brand = 'brand';
    case Blog = 'blog';

    public function label(): string
    {
        return match ($this) {
            self::Home => 'صفحه اصلی',
            self::Service => 'صفحه خدمت/محصول',
            self::Support => 'صفحه پشتیبان',
            self::Brand => 'صفحه برند',
            self::Blog => 'مقاله وبلاگ',
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

<?php

namespace App\Enums\SeoPlan;

enum KeywordRole: string
{
    case Main = 'main';
    case Secondary = 'secondary';
    case Longtail = 'longtail';
    case ArticleMain = 'article_main';

    public function label(): string
    {
        return match ($this) {
            self::Main => 'اصلی',
            self::Secondary => 'فرعی',
            self::Longtail => 'لانگ‌تیل',
            self::ArticleMain => 'کلمه‌ی اصلی مقاله',
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

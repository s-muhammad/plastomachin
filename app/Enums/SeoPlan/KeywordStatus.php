<?php

namespace App\Enums\SeoPlan;

enum KeywordStatus: string
{
    case Planned = 'planned';
    case Writing = 'writing';
    case Published = 'published';
    case NeedsReview = 'needs_review';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'برنامه‌ریزی‌شده',
            self::Writing => 'در حال نوشتن',
            self::Published => 'منتشر شد',
            self::NeedsReview => 'نیاز به بازنگری',
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

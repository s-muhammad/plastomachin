<?php

namespace App\Support;

use Illuminate\Support\Str;

class Slug
{
    /**
     * Build a readable slug that keeps Persian letters and numbers.
     */
    public static function make(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/[\s\x{200c}]+/u', '-', $text);
        $text = preg_replace('/[^\p{L}\p{N}-]+/u', '', $text);
        $text = preg_replace('/-+/', '-', $text);

        return trim(Str::lower($text), '-');
    }

    /**
     * Make a slug unique for the given model, ignoring the current record.
     */
    public static function unique(string $model, ?string $text, string $fallback, ?int $ignoreId = null): string
    {
        $base = static::make($text ?: $fallback);
        $base = $base !== '' ? $base : 'page';
        $slug = $base;
        $i = 2;

        while ($model::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}

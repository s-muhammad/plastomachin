<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoPage extends Model
{
    protected $fillable = [
        'key', 'label', 'title', 'description', 'canonical',
        'og_title', 'og_description', 'og_image',
    ];

    /**
     * Cached SEO data for a public page. Returns an empty array when missing.
     */
    public static function data(string $key): array
    {
        return Cache::rememberForever("seo.page.{$key}", function () use ($key) {
            return static::query()->where('key', $key)->first()?->toArray() ?? [];
        });
    }

    protected static function booted(): void
    {
        static::saved(function (SeoPage $page) {
            Cache::forget("seo.page.{$page->key}");
            Cache::forget("seo.page.{$page->getOriginal('key')}");
        });

        static::deleted(function (SeoPage $page) {
            Cache::forget("seo.page.{$page->key}");
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ServicePage extends Model
{
    protected $fillable = [
        'title', 'slug', 'h1', 'excerpt', 'body', 'image',
        'seo_title', 'seo_description', 'canonical',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function faqs()
    {
        return $this->hasMany(Faq::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): string
    {
        return static::imageUrl($this->image);
    }

    public static function imageUrl(?string $path): string
    {
        if (! $path) {
            return asset('images/placeholder-service.svg');
        }

        return str_starts_with($path, 'images/')
            ? asset($path)
            : Storage::disk('public')->url($path);
    }

    public static function cachedActive(): array
    {
        return Cache::rememberForever('site.services', function () {
            return static::query()->active()->orderBy('sort_order')->orderBy('id')->get()->toArray();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('site.services');
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }
}

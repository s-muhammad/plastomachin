<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function defaults(): array
    {
        return config('site.defaults', []);
    }

    /**
     * All settings, merged with defaults and cached in one key.
     */
    public static function values(): array
    {
        $stored = Cache::rememberForever('site.settings', function () {
            return static::query()->pluck('value', 'key')->all();
        });

        return array_merge(static::defaults(), $stored);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::values()[$key] ?? $default;
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value],
            );
        }

        Cache::forget('site.settings');
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('site.settings'));
        static::deleted(fn () => Cache::forget('site.settings'));
    }
}

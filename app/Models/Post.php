<?php

namespace App\Models;

use App\Support\Jalali;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'image',
        'seo_title', 'seo_description', 'canonical',
        'status', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function getImageUrlAttribute(): string
    {
        return ServicePage::imageUrl($this->image);
    }

    public function getJalaliDateAttribute(): string
    {
        return Jalali::date($this->published_at ?? $this->created_at);
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published'
            && (! $this->published_at || $this->published_at->isPast());
    }
}

<?php

namespace App\Models;

use App\Enums\SeoPlan\Competition;
use App\Enums\SeoPlan\KeywordRole;
use App\Enums\SeoPlan\KeywordStatus;
use App\Enums\SeoPlan\PageType;
use App\Enums\SeoPlan\SearchIntent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeoKeyword extends Model
{
    protected $fillable = [
        'keyword', 'role', 'target_page', 'target_url', 'page_type', 'intent',
        'priority', 'volume', 'competition', 'position', 'status',
        'content_title', 'secondary_keywords', 'internal_links', 'notes',
    ];

    protected $casts = [
        'priority' => 'integer',
        'volume' => 'integer',
        'position' => 'integer',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(SeoTask::class, 'keyword_id');
    }

    /**
     * Apply the keyword tab filters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('keyword', 'like', "%{$search}%")
                        ->orWhere('target_page', 'like', "%{$search}%")
                        ->orWhere('content_title', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $value) => $query->where('status', $value))
            ->when($filters['priority'] ?? null, fn (Builder $query, string $value) => $query->where('priority', (int) $value))
            ->when($filters['page_type'] ?? null, fn (Builder $query, string $value) => $query->where('page_type', $value))
            ->when($filters['target_page'] ?? null, fn (Builder $query, string $value) => $query->where('target_page', $value))
            ->when($filters['intent'] ?? null, fn (Builder $query, string $value) => $query->where('intent', $value))
            ->when($filters['writing_plan'] ?? null, fn (Builder $query) => $query->writingPlan());
    }

    public function scopeWritingPlan(Builder $query): Builder
    {
        return $query->where('status', KeywordStatus::Planned->value)
            ->where('priority', 1);
    }

    public function scopeSort(Builder $query, ?string $column, ?string $direction): Builder
    {
        $allowed = [
            'keyword', 'role', 'target_page', 'page_type', 'intent', 'priority',
            'volume', 'competition', 'position', 'status', 'created_at',
        ];

        $column = in_array($column, $allowed, true) ? $column : 'priority';
        $direction = $direction === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($column, $direction)->orderBy('id');
    }

    public function getRoleLabelAttribute(): string
    {
        return KeywordRole::tryFrom($this->role)?->label() ?? $this->role;
    }

    public function getPageTypeLabelAttribute(): string
    {
        return PageType::tryFrom($this->page_type)?->label() ?? $this->page_type;
    }

    public function getIntentLabelAttribute(): string
    {
        return SearchIntent::tryFrom($this->intent)?->label() ?? $this->intent;
    }

    public function getStatusLabelAttribute(): string
    {
        return KeywordStatus::tryFrom($this->status)?->label() ?? $this->status;
    }

    public function getCompetitionLabelAttribute(): string
    {
        return Competition::tryFrom((string) $this->competition)?->label() ?? '—';
    }

    public function getSafeTargetUrlAttribute(): ?string
    {
        $url = $this->target_url;

        if (! is_string($url) || $url === '') {
            return null;
        }

        return str_starts_with($url, '/')
            || str_starts_with($url, 'http://')
            || str_starts_with($url, 'https://')
            ? $url
            : null;
    }
}

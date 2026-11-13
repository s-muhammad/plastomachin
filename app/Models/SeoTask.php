<?php

namespace App\Models;

use App\Enums\SeoPlan\TaskCategory;
use App\Enums\SeoPlan\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoTask extends Model
{
    protected $fillable = [
        'title', 'description', 'category', 'priority', 'status',
        'keyword_id', 'due_date', 'done_at',
    ];

    protected $casts = [
        'priority' => 'integer',
        'due_date' => 'date',
        'done_at' => 'datetime',
    ];

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(SeoKeyword::class, 'keyword_id');
    }

    /**
     * Apply the task tab filters.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, fn (Builder $query, string $value) => $query->where('category', $value))
            ->when($filters['priority'] ?? null, fn (Builder $query, string $value) => $query->where('priority', (int) $value));
    }

    public function changeStatus(string $status): void
    {
        $this->status = $status;
        $this->done_at = $status === TaskStatus::Done->value
            ? ($this->done_at ?? now())
            : null;

        $this->save();
    }

    public function getCategoryLabelAttribute(): string
    {
        return TaskCategory::tryFrom($this->category)?->label() ?? $this->category;
    }

    public function getStatusLabelAttribute(): string
    {
        return TaskStatus::tryFrom($this->status)?->label() ?? $this->status;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date !== null
            && $this->status !== TaskStatus::Done->value
            && $this->due_date->lt(today());
    }
}

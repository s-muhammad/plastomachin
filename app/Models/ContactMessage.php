<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'phone', 'message', 'source_page', 'status'];

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'followed' ? 'پیگیری‌شده' : 'جدید';
    }
}

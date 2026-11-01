<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['service_page_id', 'question', 'answer', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function servicePage()
    {
        return $this->belongsTo(ServicePage::class);
    }

    public function scopeGeneral($query)
    {
        return $query->whereNull('service_page_id');
    }
}

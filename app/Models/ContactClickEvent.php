<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactClickEvent extends Model
{
    protected $fillable = [
        'type',
        'page_url',
        'link_url',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];
}

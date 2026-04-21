<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'component_type',
        'content',
        'settings',
        'sort_order',
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'title',
        'slug',
        'is_home',
        'status',
        'sort_order',   // 添加这一行
    ];

    protected $casts = [
        'is_home' => 'boolean',
        'status' => 'boolean',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function components()
    {
        return $this->hasMany(PageComponent::class);
    }
}
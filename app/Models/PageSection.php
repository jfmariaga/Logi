<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    use HasFactory;
     protected $fillable = [
        'page_id',
        'type',
        'title',
        'order',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /* =====================
     | Relaciones
     ===================== */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function items()
    {
        return $this->hasMany(SectionItem::class)
            ->orderBy('order');
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    /* =====================
     | Scopes
     ===================== */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, string $type)
    {
        return $query->where('type', $type);
    }
}

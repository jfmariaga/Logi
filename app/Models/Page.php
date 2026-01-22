<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    /* =====================
     | Relaciones
     ===================== */
    public function sections()
    {
        return $this->hasMany(PageSection::class)
            ->orderBy('order');
    }

    /* =====================
     | Scopes
     ===================== */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySlug(Builder $query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    protected $casts = [
        'settings' => 'array',
    ];

    public function headerSettings(): array
    {
        return optional(
            $this->sections->firstWhere('type', 'header')
        )->settings ?? [];
    }
}

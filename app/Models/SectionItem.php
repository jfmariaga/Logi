<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionItem extends Model
{
    use HasFactory;
     protected $fillable = [
        'page_section_id',
        'title',
        'content',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* =====================
     | Relaciones
     ===================== */
    public function section()
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }

    /* =====================
     | Scopes
     ===================== */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }
}

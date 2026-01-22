<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

     protected $fillable = [
        'path',
        'type',
        'alt',
        'page_section_id',
    ];

    /* =====================
     | Relaciones
     ===================== */
    public function section()
    {
        return $this->belongsTo(PageSection::class);
    }
}

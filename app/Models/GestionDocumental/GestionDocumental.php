<?php

namespace App\Models\GestionDocumental;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GestionDocumental extends Model
{
    use HasFactory;

    protected $table   = 'gestion_documental' ;
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

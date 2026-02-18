<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerceroFirma extends Model
{
    protected $fillable = [
        'tercero_id',
        'tipo',
        'archivo'
    ];

    public function tercero()
    {
        return $this->belongsTo(Tercero::class);
    }
}

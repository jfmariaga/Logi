<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerceroDocumento extends Model
{
    protected $fillable = [
        'tercero_id',
        'tipo_documento',
        'archivo',
        'obligatorio',
        'cargado'
    ];

    public function tercero()
    {
        return $this->belongsTo(Tercero::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerceroFormulario extends Model
{
    protected $fillable = [
        'tercero_id',
        'seccion',
        'campo',
        'valor',
        'obligatorio',
        'tipo_campo'
    ];

    public function tercero()
    {
        return $this->belongsTo(Tercero::class);
    }
}

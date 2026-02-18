<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaFirma extends Model
{
    protected $fillable = [
        'entrega_id',
        'archivo',
        'fecha_firma'
    ];

    protected $dates = [
        'fecha_firma'
    ];

    public function entrega()
    {
        return $this->belongsTo(Entrega::class);
    }
}

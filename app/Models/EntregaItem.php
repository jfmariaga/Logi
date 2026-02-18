<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaItem extends Model
{
    protected $fillable = [
        'entrega_id',
        'producto_id',
        'talla',
        'cantidad'
    ];

    public function entrega()
    {
        return $this->belongsTo(Entrega::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}

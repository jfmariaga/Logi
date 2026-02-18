<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioMovimiento extends Model
{
    protected $fillable = [
        'inventario_id',
        'producto_id',
        'user_id',
        'tipo',
        'cantidad_anterior',
        'cantidad_movimiento',
        'cantidad_nueva',
        'talla',
        'descripcion'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

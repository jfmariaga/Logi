<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'tipo',
        'referencia',
        'requiere_talla',
        'descripcion',
        'talla',
        'activo'
    ];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    public function items()
    {
        return $this->hasMany(EntregaItem::class);
    }
}

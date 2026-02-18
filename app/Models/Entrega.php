<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $fillable = [
        'user_id',
        'tipo',
        'responsable_id',
        'observaciones',
        'estado',
        'fecha_envio',
        'fecha_recepcion'
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(EntregaItem::class);
    }

    public function firma()
    {
        return $this->hasOne(EntregaFirma::class);
    }

    public function estaPendienteFirma()
    {
        return $this->estado === 'pendiente_firma';
    }

    public function estaFirmada()
    {
        return in_array($this->estado, ['firmada', 'finalizada']);
    }
}

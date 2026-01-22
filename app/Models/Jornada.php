<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sede_id',
        'inicio',
        'fin',
        'minutos_trabajados',
        'cerrada',
        'fuera_sede',
        'salida_fuera_sede'
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
        'cerrada' => 'boolean',
        'fuera_sede' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function sedeSalida()
    {
        return $this->belongsTo(Sede::class, 'sede_salida_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marcacion extends Model
{
    use HasFactory;

    protected $table = 'marcaciones';
    protected $fillable = [
        'user_id',
        'sede_id',
        'tipo',
        'latitud',
        'longitud',
        'distancia_metros',
        'en_sitio',
        'ip',
        'user_agent',
        'fecha_hora',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'en_sitio' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}

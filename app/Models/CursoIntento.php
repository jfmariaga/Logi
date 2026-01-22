<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoIntento extends Model
{
    protected $fillable = [
        'curso_id','user_id','intento_numero','nota','aprobado','fecha_inicio','fecha_fin'
    ];

    protected $casts = [
        'aprobado' => 'boolean',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function respuestas()
    {
        return $this->hasMany(CursoRespuestaUsuario::class, 'intento_id');
    }
}

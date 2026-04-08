<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoRespuestaUsuario extends Model
{
    protected $table = 'curso_respuestas_usuario';

    protected $fillable = ['intento_id', 'pregunta_id', 'respuesta_id', 'es_correcta'];

    // public function intento()
    // {
    //     return $this->belongsTo(CursoIntento::class, 'intento_id');
    // }

    protected $casts = [
        'es_correcta' => 'boolean',
    ];

    public function intento()
    {
        return $this->belongsTo(CursoIntento::class, 'intento_id');
    }

    public function pregunta()
    {
        return $this->belongsTo(CursoPregunta::class, 'pregunta_id');
    }

    public function respuesta()
    {
        return $this->belongsTo(CursoRespuesta::class, 'respuesta_id');
    }
}

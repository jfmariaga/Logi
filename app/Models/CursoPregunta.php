<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoPregunta extends Model
{
    protected $fillable = ['curso_id','pregunta','tipo'];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function respuestas()
    {
        return $this->hasMany(CursoRespuesta::class, 'pregunta_id');
    }
}

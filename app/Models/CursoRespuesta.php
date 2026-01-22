<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoRespuesta extends Model
{
    protected $fillable = ['pregunta_id','respuesta','es_correcta'];

    public function pregunta()
    {
        return $this->belongsTo(CursoPregunta::class, 'pregunta_id');
    }
}

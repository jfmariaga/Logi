<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'nota_minima',
        'max_intentos',
        'activo',
        'tiempo_minutos'
    ];

    public function materiales()
    {
        return $this->hasMany(CursoMaterial::class);
    }

    public function preguntas()
    {
        return $this->hasMany(CursoPregunta::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(CursoAsignacion::class);
    }

    public function intentos()
    {
        return $this->hasMany(CursoIntento::class);
    }

    public function progresos()
    {
        return $this->hasMany(CursoProgreso::class);
    }
}

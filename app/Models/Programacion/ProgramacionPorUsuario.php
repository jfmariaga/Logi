<?php

namespace App\Models\Programacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramacionPorUsuario extends Model
{
    use HasFactory;

    protected $table   = 'programacion_por_usuarios' ;
    protected $guarded = [];
    public $timestamps = false;
}

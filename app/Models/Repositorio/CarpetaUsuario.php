<?php

namespace App\Models\Repositorio;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarpetaUsuario extends Model
{
    protected $table   = 'carpetas_por_usuarios' ;
    protected $guarded = [];
    public $timestamps = false;
    
}

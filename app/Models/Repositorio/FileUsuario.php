<?php

namespace App\Models\Repositorio;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUsuario extends Model
{
    use HasFactory;

    protected $table   = 'file_por_usuarios' ;
    protected $guarded = [];
    public $timestamps = false;
}


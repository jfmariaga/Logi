<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoMaterial extends Model
{
    protected $table = 'curso_materiales';
    protected $fillable = ['curso_id','tipo','titulo','archivo_path','url','orden'];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}

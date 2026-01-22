<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CursoAsignacion extends Model
{
    protected $table   = 'curso_asignaciones' ;
    protected $fillable = ['curso_id', 'user_id', 'sede_id', 'rol_id'];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
}

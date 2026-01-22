<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoProgreso extends Model
{
    use HasFactory;

    protected $fillable = [
        'curso_id',
        'user_id',
        'fecha_inicio',
        'fecha_materiales_completados',
        'fecha_finalizado',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_materiales_completados' => 'datetime',
        'fecha_finalizado' => 'datetime',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

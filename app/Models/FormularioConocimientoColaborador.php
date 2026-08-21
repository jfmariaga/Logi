<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormularioConocimientoColaborador extends Model
{
    protected $table = 'formularios_conocimiento_colaboradores';

    protected $fillable = [
        'user_id',
        'datos',
        'firma',
        'estado',
        'observaciones',
        'revisado_por',
        'enviado_at',
        'revisado_at',
    ];

    protected function casts(): array
    {
        return [
            'datos' => 'array',
            'enviado_at' => 'datetime',
            'revisado_at' => 'datetime',
        ];
    }

    public function colaborador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }
}

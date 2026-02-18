<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tercero extends Model
{
    protected $fillable = [
        'tipo_identificacion',
        'identificacion',
        'password',
        'tipo',
        'estado',
        'progreso',
        'enviado'
    ];

    public function formularios()
    {
        return $this->hasMany(TerceroFormulario::class);
    }

    public function documentos()
    {
        return $this->hasMany(TerceroDocumento::class);
    }

    public function firma()
    {
        return $this->hasOne(TerceroFirma::class);
    }

    public function getNombreAttribute()
    {
        $nombre = $this->formularios()
            ->whereIn('campo', ['nombre_completo', 'razon_social'])
            ->orderByRaw("FIELD(campo, 'razon_social', 'nombre_completo')")
            ->first();

        return $nombre->valor ?? '—';
    }
}

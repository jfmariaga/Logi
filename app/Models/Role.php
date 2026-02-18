<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\Repositorio\Carpeta;
use App\Models\Repositorio\File;

class Role extends SpatieRole
{
    public function carpetasCompartidas()
    {
        return $this->belongsToMany(Carpeta::class, 'carpetas_por_usuarios', 'role_id', 'carpeta_id');
    }

    public function filesCompartidos()
    {
        return $this->belongsToMany(File::class, 'file_por_usuarios', 'role_id', 'file_id');
    }
}
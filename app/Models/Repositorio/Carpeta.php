<?php

namespace App\Models\Repositorio;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Repositorio\CarpetaUsuario;


class Carpeta extends Model
{
    protected $table   = 'repositorio_carpetas' ;
    protected $guarded = [];
    public $timestamps = false;

        // Carpeta padre
    public function parent()
    {
        return $this->belongsTo(Carpeta::class, 'parent_id');
    }
    
    // Subcarpetas (hijas)
    public function subcarpetas()
    {
        return $this->hasMany(Carpeta::class, 'parent_id');
    }

    public function usuarios()
    {
        return $this->hasMany(CarpetaUsuario::class, 'carpeta_id');
    }
  
    public function roles()
    {
        return $this->hasMany(CarpetaUsuario::class, 'carpeta_id');
    }
    
    // Todas las subcarpetas recursivamente
    public function subcarpetasRecursivas()
    {
        return $this->subcarpetas()->with('subcarpetasRecursivas');
    }
    

}

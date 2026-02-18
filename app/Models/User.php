<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use App\Models\Repositorio\Carpeta;
use App\Models\Repositorio\File;
use App\Models\Repositorio\CarpetaUsuario;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $guarded = [];
    public $timestamps = false;

    protected $hidden = [
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function carpetasCompartidas()
    {
        return $this->belongsToMany(Carpeta::class, 'carpetas_por_usuarios', 'user_id', 'carpeta_id');
    }

    public function filesCompartidos()
    {
        return $this->belongsToMany(File::class, 'file_por_usuarios', 'user_id', 'file_id');
    }

    public function jornadas()
    {
        return $this->hasMany(Jornada::class);
    }

    public function marcaciones()
    {
        return $this->hasMany(Marcacion::class);
    }

    public function cursoProgresos()
    {
        return $this->hasMany(CursoProgreso::class);
    }

    public function cursoIntentos()
    {
        return $this->hasMany(CursoIntento::class);
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'usuario_id');
    }
}

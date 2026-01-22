<?php

namespace App\Models\Programacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Sede;

class Programacion extends Model
{
    use HasFactory;
    protected $table   = 'programaciones' ;
    protected $guarded = [];
    public $timestamps = false;

    public function personal()
    {
        return $this->belongsToMany(User::class, 'programacion_por_usuarios', 'programacion_id', 'user_id');
    }
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}

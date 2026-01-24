<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use Spatie\Permission\Models\Role;

class Notificacion extends Model
{
    use HasFactory;

    protected $table   = 'notificaciones' ;
    protected $guarded = [];
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
    public function rol()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigNotificacion extends Model
{
    protected $table = 'config_notificaciones';

    protected $fillable = [
        'evento',
        'rol'
    ];
}

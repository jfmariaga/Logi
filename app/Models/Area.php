<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}

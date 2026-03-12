<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}

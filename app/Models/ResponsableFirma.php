<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponsableFirma extends Model
{
    protected $fillable = ['user_id', 'archivo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models\Repositorio;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    protected $table   = 'repositorio_files' ;
    protected $guarded = [];
    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function usuarios()
    {
        return $this->hasMany(FileUsuario::class, 'file_id');
    }
    public function roles()
    {
        return $this->hasMany(FileUsuario::class, 'file_id');
    }
}

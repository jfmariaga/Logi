<?php

namespace App\Models\Repositorio;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    use HasFactory;

    protected $table   = 'repositorio_files' ;
    protected $guarded = [];
    public $timestamps = false;
    
    /**
     * Los campos de DocSpace
     */
     /*
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'ult_date_docspace' => 'datetime',
    ];
    */

    /**
     * Verificar si el archivo tiene un documento en DocSpace
     */
    public function hasDocSpaceDocument(): bool
    {
        return !empty($this->docspace_id);
    }

    /**
     * Actualizar la fecha de último acceso en DocSpace
     */
    public function touchDocSpaceAccess(): void
    {
        $this->update(['ult_date_docspace' => now()]);
    }

    /**
     * Limpiar datos de DocSpace
     */
    public function clearDocSpaceData(): void
    {
        $this->update([
            'docspace_id' => null,
            'link_edit' => null,
            'link_view' => null,
            'public_url' => null,
            'docspace_request_token' => null,
            'ult_date_docspace' => null,
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Carpeta::class, 'carpeta_id');
    }

    public function usuarios()
    {
        return $this->hasMany(FileUsuario::class, 'file_id');
    }
    public function roles()
    {
        return $this->hasMany(FileUsuario::class, 'file_id');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(FileHistoral::class, 'file_id')
            ->with('user')
            ->orderByDesc('created_at');
    }
}

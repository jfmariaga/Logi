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

    // Obtener tamaño formateado
    public function getFormattedSizeAttribute(): string
    {
        $size = $this->size;
        $units = ['KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    // Obtener icono según extensión
    public function getIconAttribute(): string
    {
        $icons = [
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'ppt' => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint',
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'gif' => 'fa-file-image',
            'mp4' => 'fa-file-video',
            'mov' => 'fa-file-video',
            'avi' => 'fa-file-video',
            'mp3' => 'fa-file-audio',
            'zip' => 'fa-file-archive',
            'rar' => 'fa-file-archive',
        ];

        return $icons[$this->extension] ?? 'fa-file';
    }
}

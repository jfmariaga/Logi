<?php

namespace App\Livewire\GestionDocumental;

use Livewire\Component;

use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\GestionDocumental\GestionDocumental as Documento;

class GestionDocumental extends Component
{
    use WithFileUploads;

    public $cargando        = false;
    public $files           = []; // cargar archivos
    public $uploadedFiles   = []; // archivos ya cargados
    public $isDragging      = false; // se activa cuando se esta cargando algo
    public $maxSize         = 10240; // 10MB en KB
    public $allowedMimes    = [
        'image/*',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
        'application/zip',
        'video/*',
        'audio/*'
    ];

    protected $rules = [
        'files.*' => 'max:10240', // 10MB máximo
    ];

    protected $messages = [
        'files.*.max' => 'El archivo no debe superar los 10MB',
        'files.*.mimes' => 'Tipo de archivo no permitido',
    ];

    public function mount()
    {
        $this->getDocumentos();
    }

    public function getDocumentos(){
        $this->uploadedFiles = Documento::orderBy('original_name')->get();
        if( $this->uploadedFiles ){
            $this->uploadedFiles = $this->uploadedFiles->toArray();
        }
    }

    // hook se llama solo cuando hay cambios en el input de files
    public function updatedFiles()
    {
        $this->validate();

        foreach ($this->files as $file) {

            // guardamos el archivo
            $path = $file->store('gestion-documental', 'public');

            $new_file = Documento::create([
                'original_name' => $file->getClientOriginalName(),
                'stored_name'   => $file->hashName(),
                'size'          => $file->getSize(),
                'mime_type'     => $file->getMimeType(),
                'extension'     => $file->getClientOriginalExtension(),
                'url'           => Storage::url($path),
                'user_id'       => Auth::check() ? Auth::id() : null,
            ]);
        
        }

        $this->files    = [];
        $this->cargando = false;
        $this->getDocumentos();
    }

    public function eliminarArchivo($file)
    {
        $this->skipRender(); 
        // $file = $this->uploadedFiles[$index];
        $file_exist = Documento::find( $file['id'] );

        if( isset( $file_exist->id ) ){
            $file_exist->delete(); // borramos de la DB
            Storage::disk('public')->delete('gestion-documental/' . $file_exist->stored_name);

        }

        return true;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function render()
    {
        return view('livewire.gestion-documental.gestion-documental')->title('Gestión documental');
    }
}

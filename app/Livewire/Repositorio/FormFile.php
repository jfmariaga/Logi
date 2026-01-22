<?php

namespace App\Livewire\Repositorio;

use Livewire\Component;

use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Repositorio\File;
use App\Models\Repositorio\Carpeta;
use App\Models\Repositorio\CarpetaUsuario;

class FormFile extends Component
{
    use WithFileUploads;

    public $files = [];
    public $folderId = null;
    public $uploadProgress = 0;
    public $isUploading = false;
    public $maxFileSize = 10240; // 10MB en KB
    public $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'pdf',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'txt', 'zip', 'rar', 'mp4', 'mp3'
    ];
    
    protected $listeners = ['folderSelected' => 'setFolder'];

    public $users = [];
    public $file, $loading;

    public function mount(){
        $this->users = User::where('status', 1)->get();
    }

    public function setFolder($folderId)
    {
        $this->folderId = $folderId;
    }

    public function save()
    {
        $this->validate([
            'file.*' => [
                'max:' . $this->maxFileSize,
                function ($attribute, $value, $fail) {
                    $extension = $value->getClientOriginalExtension();
                    if (!in_array(strtolower($extension), $this->allowedExtensions)) {
                        $fail("La extensión .$extension no está permitida.");
                    }
                }
            ]
        ]);

        // $this->uploadFiles();
        // $this->saveFile($this->file);

    }

    public function uploadFiles()
    {
        $this->isUploading = true;
        $this->uploadProgress = 0;
        
        $totalFiles = count($this->files);
        $processed = 0;

        foreach ($this->files as $file) {
            try {
                $this->saveFile($file);
                $processed++;
                $this->uploadProgress = ($processed / $totalFiles) * 100;
            } catch (\Exception $e) {
                $this->addError('upload', 'Error al subir: ' . $e->getMessage());
            }
        }

        $this->files = [];
        $this->isUploading = false;
        $this->uploadProgress = 0;
        
        $this->dispatch('filesUploaded');
        session()->flash('message', 'Archivos subidos exitosamente.');
    }

    private function saveFile($uploadedFile)
    {
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $fileName = Str::random(40) . '.' . $extension;
        $fileSize = round($uploadedFile->getSize() / 1024, 2); // Convertir a KB
        $mimeType = $uploadedFile->getMimeType();

        // Crear estructura de carpetas por año/mes
        $folderPath = 'uploads/' . date('Y') . '/' . date('m');
        
        // Guardar archivo
        $path = $uploadedFile->storeAs($folderPath, $fileName, 'public');

        // Guardar en base de datos
        File::create([
            'name' => $fileName,
            'original_name' => $originalName,
            'path' => $path,
            'extension' => $extension,
            'mime_type' => $mimeType,
            'size' => $fileSize,
            'user_id' => auth()->id(),
            'folder_id' => $this->folderId,
        ]);
    }

    public function removeFile($fileId)
    {
        $file = File::find($fileId);
        
        if ($file && $file->user_id === auth()->id()) {
            Storage::disk('public')->delete($file->path);
            $file->delete();
            
            $this->dispatch('fileRemoved');
            session()->flash('message', 'Archivo eliminado exitosamente.');
        }
    }

    public function downloadFile($fileId)
    {
        $file = File::find($fileId);
        
        if ($file && $file->user_id === auth()->id()) {
            return Storage::disk('public')->download($file->path, $file->original_name);
        }
        
        abort(404);
    }

    public function render()
    {
        $files = File::where('user_id', auth()->id())
            ->when($this->folderId, function ($query) {
                return $query->where('folder_id', $this->folderId);
            })
            ->latest()
            ->paginate(20);


        return view('livewire.repositorio.form-file', compact('files'));
    }
}

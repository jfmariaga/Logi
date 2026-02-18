<?php

namespace App\Livewire\Repositorio;

use Livewire\Component;

use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

use App\Models\User;
use App\Models\Repositorio\File;
use App\Models\Repositorio\Carpeta;
use App\Models\Repositorio\CarpetaUsuario;
use App\Models\Repositorio\FileUsuario;

class FormFile extends Component
{
    use WithFileUploads;

    public $files = [];
    public $folder_id = null;
    public $isUploading = false;
    public $maxFileSize = 10240; // 10MB en KB

    public $form_file = [];
    public $loading_file;
    
    protected $listeners = ['changeCarpeta' => 'changeCarpeta'];

    public $users = [], $roles = [];
    public $file, $loading;

    public function mount(){
        $this->users = User::where('status', 1)->get();
        $this->roles = Role::all();

        $this->vaciarFormFile();
    }

    public function vaciarFormFile(){
        $this->form_file = [
            'id'            => 0,
            'file'          => null,
            'type_tmp'      => null,
            'name_tmp'      => null,
            'nombre'        => '',
            'roles'         => [],
            'usuarios'      => []
        ]; 
    }

    public function changeCarpeta($id = null, $home = null){
        $this->folder_id = $id;
    }

    public function save()
    {

        if( $this->form_file['id'] ){

            $file_item = File::find($this->form_file['id']);

            if( isset( $file_item->id ) ){

                // actualizamos los datos del archivo
                $file_item->update([
                    'name'        => $this->form_file['nombre'] ?? $file_item->name,
                    'updated_at'  => now()
                ]);
            }

        }else{

            $this->validate([
                'form_file.file' => 'required|file',
                'form_file.nombre' => 'nullable|string|max:255',
            ], [
                'form_file.file.required' => 'El archivo es obligatorio.',
                'form_file.file.file' => 'El campo debe ser un archivo válido.',
                'form_file.file.max' => 'El archivo no debe superar los 10MB.',
                'form_file.file.mimes' => 'Tipo de archivo no permitido.',
                'form_file.nombre.string' => 'El nombre debe ser una cadena de texto.',
                'form_file.nombre.max' => 'El nombre no debe superar los 255 caracteres.',
            ]);

            if( $this->form_file['file'] ){
                $file = $this->form_file['file'];
    
                // guardamos el archivo
                $path = $file->store('gestion-documental', 'public');
        
                // Guardar en base de datos
                $file_item = File::create([
    
                    'name'          => $this->form_file['nombre'] ?? $file->getClientOriginalName(),
                    'original_name' => $file->hashName(),
                    'size'          => $file->getSize(),
                    'mime_type'     => $file->getMimeType(),
                    'extension'     => $file->getClientOriginalExtension(),
                    'user_id'       => auth()->id(),
                    'carpeta_id'    => $this->folder_id ?? null,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }
        }

        if( isset( $file_item->id ) ){
            $this->syncFileShares($file_item);
            $this->vaciarFormFile();
            $this->dispatch('reloadSubCarpetas');
            
            return true;
        }

    }
    protected function syncFileShares(File $file_item)
    {
        FileUsuario::where('file_id', $file_item->id)->delete();
        $this->form_file['usuarios'][] = $file_item->user_id;
        $usuarios = array_unique($this->form_file['usuarios']);

        foreach ($usuarios as $id_usuario) {
            FileUsuario::create([
                'file_id' => $file_item->id,
                'user_id' => $id_usuario
            ]);
        }

        foreach ($this->form_file['roles'] as $id_role) {
            FileUsuario::create([
                'file_id' => $file_item->id,
                'role_id' => $id_role
            ]);
        }
    }

    public function render()
    {
        // para mostrar la vista temporal
        if( $this->form_file['file'] ){
            $this->form_file['type_tmp'] = $this->form_file['file']->getMimeType();
            $this->form_file['name_tmp'] = $this->form_file['file']->getClientOriginalName();
        }

        $this->loading_file = false;

        return view('livewire.repositorio.form-file');
    }
}

<?php

namespace App\Livewire\Repositorio;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

use App\Models\User;
use App\Models\Repositorio\Carpeta;
use App\Models\Repositorio\CarpetaUsuario;

class FormCarpeta extends Component
{

    public $carpetas_user = [];
    public $users = [], $roles = [];

    public $folder_id = 0; // 0 es el home
    public $loading = false;

    // variables add carpeta
    public $form_carpeta = []; 

    protected $listeners = ['changeCarpeta' => 'changeCarpeta'];

    public function mount(){
        $this->users = User::where('status', 1)->get();
        $this->roles = Role::all();
        $this->vaciarFormCarpeta();
    }

    public function vaciarFormCarpeta(){
        $this->form_carpeta = [
            'id'            => 0,
            'parent'        => 0,
            'nombre'        => '',
            'descripcion'   => '',
            'roles'      => [],
            'usuarios'      => []
        ]; 
    }

    public function changeCarpeta($id = null, $home = null){
        $this->folder_id = $id;
    }

    public function render()
    {
        return view('livewire.repositorio.form-carpeta');
    }

    public function saveCarpeta(){
        $this->validate([
            'form_carpeta.nombre' => 'required'
        ]);

        // dd( $this->form_carpeta );

        if( $this->form_carpeta['id'] ){ // editar
            $carpeta = Carpeta::find( $this->form_carpeta['id'] );

            $carpeta->nombre        = $this->form_carpeta['nombre'];
            $carpeta->descripcion   = $this->form_carpeta['descripcion'];
            $carpeta->updated_at    = now();
            $carpeta->save();

        }else{ // crear
            $carpeta= Carpeta::create([
                'parent'            => $this->folder_id,
                'nombre'            => $this->form_carpeta['nombre'],
                'descripcion'       => $this->form_carpeta['descripcion'],
                'user_id'           => Auth::id(),
                'status'            => 1,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);
        }

        if( isset( $carpeta->id ) ){

            // borramos las relaciones anteriores
            CarpetaUsuario::where('carpeta_id', $carpeta->id)->delete();

            // el propietario siempre tiene acceso
            $this->form_carpeta['usuarios'][] = $carpeta->user_id;

            // cargamos las nuevas relaciones a usuarios
            foreach( $this->form_carpeta['usuarios'] as $id_usuario ){
                CarpetaUsuario::create([
                    'carpeta_id' => $carpeta->id,
                    'user_id'    => $id_usuario
                ]);
            }

            // cargamos las nuevas relaciones a roles
            foreach( $this->form_carpeta['roles'] as $id_role ){
                CarpetaUsuario::create([
                    'carpeta_id' => $carpeta->id,
                    'role_id'    => $id_role
                ]);
            }

            $this->vaciarFormCarpeta();
            $this->dispatch('repositorio_update_menu');
            $this->dispatch('reloadSubCarpetas');
            return true;
        }
    }
}

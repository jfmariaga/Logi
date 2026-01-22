<?php

namespace App\Livewire\Repositorio;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Repositorio\Carpeta;
use App\Models\Repositorio\CarpetaUsuario;

class FormCarpeta extends Component
{

    public $carpetas_user = [];
    public $users = [];

    public $carpeta_id      = 0; // 0 es el home, carpeta en la que estoy
    public $carpeta_edit_id = null; // para editar
    public $loading = false;
    protected $listeners    = [ 'changeCarpeta'];

    public function changeCarpeta( $id = 0 ){
        $this->carpeta_id = $id;
    }

    // variables add carpeta
    public $form_carpeta = []; 

    public function mount(){
        $this->users = User::where('status', 1)->get();
        $this->vaciarFormCarpeta();
    }

    public function vaciarFormCarpeta(){
        $this->form_carpeta = [
            'id'            => 0,
            'parent'        => 0,
            'nombre'        => '',
            'descripcion'   => '',
            'privada'       => 0,
            'usuarios'      => []
        ]; 
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

        if( $this->carpeta_edit_id ){ // editar
            $carpeta = Carpeta::find( $this->carpeta_edit_id );

            $carpeta->nombre        = $this->form_carpeta['nombre'];
            $carpeta->descripcion   = $this->form_carpeta['descripcion'];
            $carpeta->privada       = $this->form_carpeta['privada'];
            $carpeta->updated_at    = date('Y-m-d H:i');
            $carpeta->save();

        }else{ // crear
            $carpeta= Carpeta::create([
                'parent'            => $this->carpeta_id,
                'nombre'            => $this->form_carpeta['nombre'],
                'descripcion'       => $this->form_carpeta['descripcion'],
                'privada'           => $this->form_carpeta['privada'],
                'user_id'           => Auth::id(),
                'status'            => 1,
                'created_at'        => date('Y-m-d H:i'),
                'updated_at'         => date('Y-m-d H:i')
            ]);
        }

        if( isset( $carpeta->id ) ){

            // borramos las relaciones anteriores
            CarpetaUsuario::where('carpeta_id', $carpeta->id)->delete();

            // el propietario siempre tiene acceso
            if( !isset( $this->form_carpeta['usuarios'][ $carpeta->user_id ] ) ){
                $this->form_carpeta['usuarios'][] = $carpeta->user_id ;
            }

            // cargamos las nuevas relaciones
            foreach( $this->form_carpeta['usuarios'] as $id_usuario ){
                CarpetaUsuario::create([
                    'carpeta_id' => $carpeta->id,
                    'user_id'    => $id_usuario
                ]);
            }

            $this->vaciarFormCarpeta();
            $this->dispatch('repositorio_update_menu');
            $this->dispatch('reloadSubCarpetas');
            return true;
        }
    }
}

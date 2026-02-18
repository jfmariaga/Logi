<?php

namespace App\Livewire\Repositorio;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;

use App\Models\Repositorio\Carpeta;
use App\Models\Repositorio\File;
use App\Models\Repositorio\CarpetaUsuario;

class Repositorio extends Component
{

    public $loading = true;
    public $folder_id = 0; // 0 es el home
    public $carpeta;
    public $sub_carpetas = [], $files = []; // sub_carpetas y archivos de la carpeta actual
    public $carpetas_user = [], $files_user = []; // carpetas y archivos compartidos con el usuario
    public $miga_de_pan = [];
  
    protected $listeners = [ 
        'changeCarpeta' => 'changeCarpeta',
        'reloadSubCarpetas' => 'changeCarpeta'
    ];

    protected $queryString = [
        'folder_id' => ['except' => 0],
    ];

    public function mount(){
        $this->changeCarpeta( 0 );
    }

    public function render()
    {
        return view('livewire.repositorio.repositorio')->title('Repositorio');
    }

    // carga las carpetas y archivos a los que tiene acceso el usuario
    public function archivosUser(){
        $usuario = Auth::user();
        $rol_id = $usuario->roles()->get()->toArray()[0]['id'];

        $carpetas_por_mi_user = $usuario->carpetasCompartidas()->pluck('carpeta_id')->toArray();
        $carpetas_por_mi_rol  = Role::find( $rol_id )->carpetasCompartidas()->pluck('carpeta_id')->toArray();

        $files_por_mi_user = $usuario->filesCompartidos()->pluck('file_id')->toArray();
        $files_por_mi_rol  = Role::find( $rol_id )->filesCompartidos()->pluck('file_id')->toArray();

        $this->carpetas_user = $carpetas_por_mi_rol;
        foreach( $carpetas_por_mi_user as $key => $c ){
            if( !in_array($c, $carpetas_por_mi_rol) ){
                $this->carpetas_user[] = $c;
            }
        }

        $this->files_user = $files_por_mi_rol;
        foreach( $files_por_mi_user as $f ){
            if( !in_array($f, $files_por_mi_rol) ){
                $this->files_user[] = $f;
            }
        }
    }

    public function changeCarpeta( $id = 0, $home = 0 ){

        $this->archivosUser();

        if( $id || $home ){
            $this->folder_id = $id;
        }

        if( $this->folder_id ){ // carpeta normal
            $carpeta            = Carpeta::find( $this->folder_id );
            $this->carpeta      = $carpeta->toArray();
        }
        
        $this->miga_de_pan      = $this->getMigaPan( $this->folder_id );
        $this->sub_carpetas     = Carpeta::where('parent', $this->folder_id)->where('status', 1)->with('usuarios', 'roles')->get()->toArray();

        if( $this->folder_id == 0 ){
            $this->files            = File::whereNull('carpeta_id')->whereNull('fecha_delete')->with('usuarios', 'roles')->get()->toArray();
        }else{
            $this->files            = File::where('carpeta_id', $this->folder_id)->whereNull('fecha_delete')->with('usuarios', 'roles')->get()->toArray();
        }

        $this->loading = false;
    }

    // arma la miga de pann para navegar hacia atrás
    public function getMigaPan( $id ){
        $res = [];

        if( $id ){
            $item = Carpeta::find($id);
            
            if ($item) {
                // Agregar carpeta actual
                $res[] = [
                    'id' => $item->id,
                    'nombre' => $item->nombre
                ];
                
                // Si tiene parent, agregar al inicio recursivamente
                if ($item->parent) {
                    $padres = $this->getMigaPan($item->parent);
                    $res = array_merge($padres, $res);
                }
            }
        }
        
        return $res;
    }

    public function eliminarCarpeta( $id ){
        $carpeta = Carpeta::find( $id );
        $carpeta->status = 0;
        $carpeta->save();

        $this->changeCarpeta( $this->folder_id );
        return true;
    }

    public function eliminarFile( $id ){
        $file = File::find( $id );
        $file->fecha_delete = now();
        $file->id_user_delete = Auth::user()->id;
        $file->save();
        
        $this->changeCarpeta( $this->folder_id );
        return true;
    }
}

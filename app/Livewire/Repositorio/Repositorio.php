<?php

namespace App\Livewire\Repositorio;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Models\Repositorio\Carpeta;
use App\Models\Repositorio\CarpetaUsuario;

class Repositorio extends Component
{

    public $loading = true;
    public $carpeta_id = 0; // 0 es el home
    public $carpeta;
    public $sub_carpetas2, $sub_carpetas = [], $files = []; // incluye carpetas y archivos dentro de la carpeta
    public $miga_de_pan = [];
  
    protected $listeners = [ 
        'changeCarpeta' => 'changeCarpeta',
        'reloadSubCarpetas' => 'changeCarpeta'
    ];

    public function mount(){
        $this->changeCarpeta( 0 );
    }

    public function render()
    {
        return view('livewire.repositorio.repositorio')->title('Repositorio');
    }

    public function changeCarpeta( $id = 0, $home = 0 ){

        if( $id || $home ){
            $this->carpeta_id = $id;
        }

        if( $this->carpeta_id ){ // carpeta normal
            $carpeta            = Carpeta::find( $this->carpeta_id );
            $this->carpeta      = $carpeta->toArray();
        }
        
        $this->miga_de_pan      = $this->getMigaPan( $this->carpeta_id );
        $this->sub_carpetas     = Carpeta::where('parent', $this->carpeta_id)->where('status', 1)->with('usuarios')->get()->toArray();

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
        return true;
    }
}

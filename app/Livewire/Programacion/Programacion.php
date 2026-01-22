<?php

namespace App\Livewire\Programacion;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;

use App\Models\Sede;
use App\Models\User;
use App\Models\Programacion\Programacion as ModelProgramacion;
use App\Models\Programacion\ProgramacionPorUsuario;

class Programacion extends Component
{

    public $programaciones = [];
    public $sedes = [], $usuarios = [], $operadores;

    // filtros
    public $filtro_tipo = 0, $filtro_sede, $filtro_usuario, $filtro_desde, $filtro_hasta;

    protected $listeners = ['getProgramaciones'];

    public function mount(){
        $this->sedes    = Sede::where('activo', 1)->orderBy('nombre')->get();
        $this->usuarios = User::where('status', 1)->with('roles')->orderBy('name')->get();

        // identificamos los operadores
        $usuarios_array = $this->usuarios->toArray();
        foreach( $usuarios_array as $u ){
            if( isset( $u['roles'][0]['id'] ) && $u['roles'][0]['id'] == '8' ){
                $this->operadores[ $u['id'] ] = [
                    'id'        => $u['id'],
                    'name'      => $u['name'],
                    'last_name' => $u['last_name'],
                    'phone'     => $u['phone'],
                    'picture'   => $u['picture'],
                    'libre'     => 1
                ]; 
            }
        }

        $hoy = date('Y-m-d');
        $this->filtro_desde = date('Y-m-d', strtotime( $hoy. ' - 15 day' ));
        $this->filtro_hasta = date('Y-m-d', strtotime( $hoy. ' + 15 day' ));
        $this->getProgramaciones();
    }

    public function getProgramaciones(){

        $programaciones = ModelProgramacion::whereBetween('desde' , [ $this->filtro_desde , $this->filtro_hasta ] )->orWhereBetween( 'hasta' , [ $this->filtro_desde , $this->filtro_hasta ]  )->with(['personal' => function($query) {
                                                                $query->select('users.id', 'users.name', 'users.last_name', 'users.phone', 'users.picture');
                                                    }, 'sede'])->get()->toArray();

        // asignamos los operadores a cada dia, para validar disponibilidad
        $desde_tmp_operadores = $this->filtro_desde;
        while( $desde_tmp_operadores <= $this->filtro_hasta ){
            $this->programaciones[ $desde_tmp_operadores ][ 'operadores' ] = $this->operadores; // asignación en blanco
            
            $desde_tmp_operadores = date('Y-m-d',  strtotime( $desde_tmp_operadores . ' + 1 day' ) );
        }

        // agrupamos por fechas
        $this->programaciones = [];
        foreach( $programaciones as $p ){

            // aplicamos filtro de sede
            if( $this->filtro_sede && $this->filtro_sede != $p['sede_id'] ){
                continue;
            }
     
            // aplicamos filtro de usuario
            if( $this->filtro_usuario ){
                $usuario_asignado = ProgramacionPorUsuario::where('programacion_id', $p['id'])->where('user_id', $this->filtro_usuario)->first();
                if( !isset( $usuario_asignado->id ) ){ // si esta programación no tiene al usuario se omite
                    continue;
                }
            }

            // marcamos lo operadores ocupados
            $copia_operadores = $this->operadores;
            foreach( $p['personal'] as $personal ){
                unset( $copia_operadores[ $personal['id'] ]); 
            }

            $desde_tmp = $p['desde'];
            // rellenamos los rangos a los que aplica esta programación

            while( $desde_tmp <= $p['hasta'] ){

                // si no se han cargado los operadores para este día
                if( !isset( $this->programaciones[ $desde_tmp ][ 'operadores' ] ) ){
                    $this->programaciones[ $desde_tmp ][ 'operadores' ] = $copia_operadores ?? null;
                }

                $this->programaciones[ $desde_tmp ]['programaciones'][] = $p;
                // aumentamos el dia, para seguir validando
                $desde_tmp = date('Y-m-d',  strtotime( $desde_tmp . ' + 1 day' ) );
            }
        }

        // // relleno, completamos los dias que estan en el rango de busqueda pero no tienen datos, por si lo piden aquí está
        // $desde_tmp_relledo = $this->filtro_desde;
        // while( $desde_tmp_relledo <= $this->filtro_hasta ){

        //     if( !isset( $this->programaciones[ $desde_tmp_relledo ] ) ){
        //         $this->programaciones[ $desde_tmp_relledo ] = []; // asignación en blanco
        //     }
        //     
        //     $desde_tmp_relledo = date('Y-m-d',  strtotime( $desde_tmp_relledo . ' + 1 day' ) );
        // }

        krsort($this->programaciones);
        // $this->programaciones = array_reverse( $this->programaciones );
    }

    public function eliminar( $id ){
        ModelProgramacion::where( 'id', $id )->delete();
        ProgramacionPorUsuario::where( 'programacion_id', $id )->delete();
        $this->getProgramaciones();

        return true;
    }

    public function render()
    {
        return view('livewire.programacion.programacion')->title('Programación');
    }
}

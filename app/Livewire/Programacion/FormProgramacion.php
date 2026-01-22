<?php

namespace App\Livewire\Programacion;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\Sede;
use App\Models\User;
use App\Models\Programacion\Programacion;
use App\Models\Programacion\ProgramacionPorUsuario;

class FormProgramacion extends Component
{
    public $form = [
        'desde'         => '',
        'hasta'         => '',
        'hora_entrada'  => null,
        'hora_salida'   => null,
        'sede_id'       => null,
        'personal'      => [],
    ];
    public $form_old = []; // para editar

    public $programaciones = [];
    public $sedes = [], $usuarios = [], $operadores;

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
    }

    public function save()
    {
        $this->validate([
            'form.sede_id' => 'required|min:1',
            'form.desde'   => 'required',
        ]);

        // sino viene hasta, es el mismo desde
        $this->form['hasta'] = $this->form['hasta'] != '' ? $this->form['hasta'] : $this->form['desde'];

        if( $this->form_old ){

            $programacion = Programacion::find( $this->form_old['id'] );

            $programacion->desde        = $this->form['desde'];
            $programacion->hasta        = $this->form['hasta'];
            $programacion->hora_entrada = $this->form['hora_entrada'];
            $programacion->hora_salida  = $this->form['hora_salida'];
            $programacion->sede_id      = $this->form['sede_id'];
            $programacion->user_id      =  Auth::check() ? Auth::id() : null;

            $programacion->save();
        }else{
            $programacion = Programacion::create([
                'desde'         => $this->form['desde'],
                'hasta'         => $this->form['hasta'],
                'hora_entrada'  => $this->form['hora_entrada'],
                'hora_salida'   => $this->form['hora_salida'],
                'sede_id'       => $this->form['sede_id'],
                'user_id'       => Auth::check() ? Auth::id() : null
            ]);
        }

        if( isset( $programacion->id ) ){

            ProgramacionPorUsuario::where( 'programacion_id', $programacion->id )->delete();

            foreach( $this->form['personal'] as $user_id ){
                ProgramacionPorUsuario::create([
                        'programacion_id'   => $programacion->id,
                        'user_id'           => $user_id
                    ]);
            }
        }

        // $this->getProgramaciones();
        $this->dispatch( 'getProgramaciones' );
        
        if ($programacion) {
            return true;
        } else {
            return false;
        }
    }

    public function render()
    {
        return view('livewire.programacion.form-programacion');
    }
}

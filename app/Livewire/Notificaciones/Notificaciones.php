<?php

namespace App\Livewire\Notificaciones;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\Notificacion;

class Notificaciones extends Component
{
    public $notas = [];

    public function mount(){
        $this->getNotas();
    }

    public function getNotas(){

        $this->id_rol_user = Auth::user()->roles->first()->id ?? 0;

        $hoy = date('Y-m-d');
        $this->notas = Notificacion::where('fecha_expired', '>=', $hoy)->where(function($query) {
                $query->whereNull('role_id')
                ->orWhere('role_id', $this->id_rol_user);
            })->with('usuario')->orderBy('id', 'desc')->get()->toArray();

        // dd( $this->notas );
    }
    public function render()
    {
        return view('livewire.notificaciones.notificaciones');
    }
}

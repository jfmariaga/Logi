<?php

namespace App\Livewire\Notificaciones;

use Livewire\Component;

use App\Models\Notificacion;

class Notificaciones extends Component
{
    public $notas = [];

    public function mount(){
        $this->getNotas();
    }

    public function getNotas(){
        $hoy_menos30 = date('Y-m-d');
        $this->notas = Notificacion::where('fecha_expired', '>=', $hoy_menos30)->with('usuario')->orderBy('id', 'desc')->get()->toArray();

        // dd( $this->notas );
    }
    public function render()
    {
        return view('livewire.notificaciones.notificaciones');
    }
}

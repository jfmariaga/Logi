<?php

namespace App\Livewire\Notificaciones;

use Livewire\Component;

use App\Models\Notificacion;

class InformacionDeInteres extends Component
{

    public $notas = [];

    protected $listeners = ['getNotas'];

    public function mount(){
        $this->getNotas();
    }

    public function getNotas(){
        $hoy_menos30 = date('Y-m-d', strtotime( ' - 30 day' ));
        $this->notas = Notificacion::where('fecha_expired', '>=', $hoy_menos30)->orderBy('id', 'desc')->get()->toArray();
    }

    public function eliminar( $id ){
        Notificacion::where('id', $id)->delete();
        $this->getNotas();
        return true;
    }

    public function render()
    {
        return view('livewire.notificaciones.informacion-de-interes')->title('Información de interes');
    }
}

<?php

namespace App\Livewire\Repositorio;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Models\Repositorio\Carpeta;
use App\Models\Repositorio\CarpetaUsuario;

class Menu extends Component
{

    // carpetas a las qye tienne acceso el usuario
    public function getCarpetas(){
        $carpetas = auth()->user()->carpetasCompartidas->toArray() ?? [];
        return json_encode( $carpetas );
    }

    public function render()
    {
        return view('livewire.repositorio.menu');
    }
}

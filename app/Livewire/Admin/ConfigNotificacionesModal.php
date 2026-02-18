<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Models\ConfigNotificacion;

class ConfigNotificacionesModal extends Component
{
    public $rolSeleccionado;
    public $roles = [];

    public $rolActual = null;
    public $ultimaActualizacion = null;

    protected $listeners = ['abrirConfigNotificaciones' => 'abrir'];

    public function abrir()
    {
        $this->roles = Role::pluck('name')->toArray();

        $config = ConfigNotificacion::where('evento','nuevo_formulario')->first();

        if($config){
            $this->rolSeleccionado = $config->rol;
            $this->rolActual = $config->rol;
            $this->ultimaActualizacion = $config->updated_at->format('d/m/Y H:i');
        }else{
            $this->rolSeleccionado = null;
            $this->rolActual = 'No configurado';
            $this->ultimaActualizacion = 'Nunca';
        }

        $this->dispatch('show-config-modal');
    }

    public function guardar()
    {
        if(!$this->rolSeleccionado){
            $this->dispatch('toast-error', msg:'Seleccione un rol');
            return;
        }

        $config = ConfigNotificacion::updateOrCreate(
            ['evento' => 'nuevo_formulario'],
            ['rol' => $this->rolSeleccionado]
        );

        $this->rolActual = $config->rol;
        $this->ultimaActualizacion = $config->updated_at->format('d/m/Y H:i');

        $this->dispatch('toast-ok', msg:'Configuración guardada');
        $this->dispatch('hide-config-modal');
    }

    public function render()
    {
        return view('livewire.admin.config-notificaciones-modal');
    }
}

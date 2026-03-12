<?php

namespace App\Livewire\Configuracion;

use Livewire\Component;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\User;

class AreasCargos extends Component
{
    public $areas;
    public $cargos;

    public $nombreArea;
    public $nombreCargo;
    public $areaCargo;

    protected $rules = [
        'nombreArea' => 'required'
    ];

    public function mount()
    {
        $this->cargar();
    }

    public function cargar()
    {
        $this->areas = Area::orderBy('nombre')->get();
        $this->cargos = Cargo::with('area')->orderBy('nombre')->get();
    }

    public function crearArea()
    {
        $this->validate();

        Area::create([
            'nombre' => $this->nombreArea
        ]);

        $this->reset('nombreArea');
        $this->cargar();
    }

    public function crearCargo()
    {
        Cargo::create([
            'nombre' => $this->nombreCargo,
            'area_id' => $this->areaCargo
        ]);

        $this->reset(['nombreCargo', 'areaCargo']);
        $this->cargar();
    }

    public function eliminarArea($id)
    {
        $area = Area::with('cargos')->find($id);

        if ($area->cargos->count() > 0) {

            $this->dispatch(
                'toast',
                type: 'warning',
                message: 'No se puede eliminar el área porque tiene cargos asociados.'
            );

            return;
        }

        $area->delete();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Área eliminada correctamente.'
        );

        $this->cargar();
    }

    public function eliminarCargo($id)
    {
        $cargo = Cargo::find($id);

        $usuarios = \App\Models\User::where('cargo_id', $cargo->id)->count();

        if ($usuarios > 0) {

            $this->dispatch(
                'toast',
                type: 'warning',
                message: 'No se puede eliminar el cargo porque tiene usuarios asignados.'
            );

            return;
        }

        $cargo->delete();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Cargo eliminado correctamente.'
        );

        $this->cargar();
    }

    public function render()
    {
        return view('livewire.configuracion.areas-cargos');
    }
}

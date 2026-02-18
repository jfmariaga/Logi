<?php

namespace App\Livewire\Contrapartes;

use Livewire\Component;

class Firma extends Component
{
    public function enviar()
    {
        if ($this->tercero->progreso < 100) {
            session()->flash('error', 'Faltan campos por diligenciar');
            return;
        }

        $this->tercero->update([
            'estado' => 'completo'
        ]);

        session()->flash('success', 'Formulario enviado correctamente');
    }

    public function render()
    {
        return view('livewire.contrapartes.firma')
            ->layout('components.layouts.contrapartes-externo');
    }
}

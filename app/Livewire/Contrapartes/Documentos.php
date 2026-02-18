<?php

namespace App\Livewire\Contrapartes;

use App\Models\TerceroDocumento;
use Livewire\Component;
use Livewire\WithFileUploads;

class Documentos extends Component
{
    use WithFileUploads;

    public $archivos = [];

    public function subir($tipo)
    {
        $path = $this->archivos[$tipo]->store('documentos');

        TerceroDocumento::updateOrCreate(
            ['tercero_id' => session('tercero_id'), 'tipo_documento' => $tipo],
            ['archivo' => $path, 'cargado' => true]
        );
    }

    public function render()
    {
        return view('livewire.contrapartes.documentos')
            ->layout('components.layouts.contrapartes-externo');
    }
}

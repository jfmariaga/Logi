<?php

namespace App\Livewire\Admin;

use App\Models\FormularioConocimientoColaborador;
use Livewire\Component;

class FormularioColaboradorDetalle extends Component
{
    public FormularioConocimientoColaborador $formulario;

    public function mount(int $id): void
    {
        $this->formulario = FormularioConocimientoColaborador::with(['colaborador', 'revisor'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.formulario-colaborador-detalle')
            ->title('Detalle del formulario de colaborador');
    }
}

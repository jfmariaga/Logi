<?php

namespace App\Livewire\Admin;

use App\Models\FormularioConocimientoColaborador;
use App\Notifications\EstadoFormularioColaborador;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class FormulariosColaboradores extends Component
{
    public function getFormularios(): array
    {
        return FormularioConocimientoColaborador::with('colaborador')
            ->latest('updated_at')
            ->get()
            ->map(fn ($formulario) => [
                'id' => $formulario->id,
                'nombre' => trim($formulario->colaborador->name . ' ' . $formulario->colaborador->last_name),
                'documento' => $formulario->colaborador->document,
                'estado' => $formulario->estado,
                'enviado' => $formulario->enviado_at?->format('d/m/Y H:i') ?? '—',
                'observaciones' => $formulario->observaciones,
            ])->all();
    }

    public function aprobar(int $id): void
    {
        $this->actualizarEstado($id, 'aprobado');
    }

    public function rechazar(int $id, string $motivo = ''): void
    {
        $this->actualizarEstado($id, 'rechazado', $motivo);
    }

    #[On('rechazar-formulario-desde-listado')]
    public function rechazarDesdeListado(int $id, string $motivo = ''): void
    {
        $this->rechazar($id, $motivo);
    }

    private function actualizarEstado(int $id, string $estado, string $motivo = ''): void
    {
        $formulario = FormularioConocimientoColaborador::findOrFail($id);
        $formulario->update([
            'estado' => $estado,
            'observaciones' => $estado === 'rechazado' ? ($motivo ?: 'Debe corregir la información del formulario.') : null,
            'revisado_por' => Auth::id(),
            'revisado_at' => now(),
        ]);

        if ($formulario->colaborador?->email) {
            try {
                $formulario->colaborador->notify(new EstadoFormularioColaborador($estado, $formulario->observaciones));
            } catch (\Throwable $exception) {
                Log::error('No se pudo notificar cambio de estado del formulario de colaborador', [
                    'formulario_id' => $formulario->id,
                    'estado' => $estado,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $this->dispatch('toast-ok', msg: 'Estado del formulario actualizado.');
    }

    public function render()
    {
        return view('livewire.admin.formularios-colaboradores')
            ->title('Formularios de colaboradores');
    }
}

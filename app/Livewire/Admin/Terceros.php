<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Tercero;

class Terceros extends Component
{
    public $resumen = [];

    public function mount()
    {
        $this->cargarResumen();
    }

    public function cargarResumen()
    {
        $this->resumen = [
            'en_proceso' => Tercero::where('estado', 'en_proceso')->count(),
            'pendientes' => Tercero::where('estado', 'enviado')->count(),
            'aprobados'  => Tercero::where('estado', 'aprobado')->count(),
            'rechazados' => Tercero::where('estado', 'rechazado')->count(),
        ];
    }

    private function badgeEstado($estado)
    {
        $map = [
            'en_proceso' => [
                'texto' => 'En proceso',
                'color' => 'warning'
            ],
            'enviado' => [
                'texto' => 'Pendiente',
                'color' => 'info'
            ],
            'aprobado' => [
                'texto' => 'Aprobado',
                'color' => 'success'
            ],
            'rechazado' => [
                'texto' => 'Rechazado',
                'color' => 'danger'
            ],
        ];

        $data = $map[$estado] ?? [
            'texto' => ucfirst(str_replace('_', ' ', $estado)),
            'color' => 'secondary'
        ];

        return [
            'html' => '<span class="badge badge-' . $data['color'] . '">' . $data['texto'] . '</span>',
            'texto' => $data['texto']
        ];
    }

    public function getTerceros()
    {
        $this->skipRender();

        $data = Tercero::with('formularios')
            ->orderByDesc('id')
            ->get()
            ->map(function ($t) {

                $estado = $this->badgeEstado($t->estado);

                return [
                    'id' => $t->id,
                    'identificacion' => $t->identificacion,
                    'nombre' => $t->nombre,
                    'tipo' => ucfirst($t->tipo),
                    'estado' => $estado['html'],
                    'progreso' => $t->progreso ?? 0,
                    'enviado' => $t->enviado ? 'Sí' : 'No',
                    'actualizado' => $t->updated_at->format('d/m/Y H:i'),
                ];
            })->toArray();

        $this->cargarResumen();

        return $data;
    }

    public function render()
    {
        return view('livewire.admin.terceros')
            ->title('Terceros');
    }
}

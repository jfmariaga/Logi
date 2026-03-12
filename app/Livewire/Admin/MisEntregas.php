<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Entrega;
use App\Models\EntregaFirma;
use App\Models\Inventario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MisEntregas extends Component
{
    public $entregas = [];
    public $entrega_id = null;
    public $firma_base64 = null;
    public $detalleEntrega = [];

    public function mount()
    {
        $this->loadEntregas();
    }

    public function loadEntregas()
    {
        $this->entregas = Entrega::with('items.producto')
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'estado' => $e->estado,
                    'created_at' => $e->created_at,
                    'total_items' => $e->items->sum('cantidad'),
                    'items' => $e->items->map(function ($i) {
                        return [
                            'producto' => $i->producto->nombre,
                            'cantidad' => $i->cantidad
                        ];
                    })
                ];
            })
            ->toArray();
    }

    public function cargarDetalle($id)
    {
        $entrega = Entrega::with('items.producto')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $this->entrega_id = $id;

        $this->detalleEntrega = $entrega->items->map(function ($i) {
            return [
                'producto' => $i->producto->nombre,
                'cantidad' => $i->cantidad
            ];
        })->toArray();
    }

    public function firmar()
    {
        if (!$this->firma_base64) {
            $this->dispatch('toast', type: 'error', message: 'Debe realizar la firma.');
            return;
        }

        DB::beginTransaction();

        try {

            $entrega = Entrega::with('items')
                ->where('id', $this->entrega_id)
                ->where('user_id', auth()->id())
                ->lockForUpdate()
                ->firstOrFail();

            if ($entrega->estado !== 'pendiente_firma') {
                throw new \Exception('Entrega ya procesada.');
            }

            foreach ($entrega->items as $item) {

                $inventario = Inventario::where('producto_id', $item->producto_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($inventario->cantidad < $item->cantidad) {
                    throw new \Exception('Stock insuficiente.');
                }

                $inventario->decrement('cantidad', $item->cantidad);
                $inventario->decrement('cantidad_reservada', $item->cantidad);
            }

            $image = str_replace('data:image/png;base64,', '', $this->firma_base64);
            $image = str_replace(' ', '+', $image);

            $fileName = 'firmas/entrega_' . $entrega->id . '_' . time() . '.png';

            Storage::disk('public')->put($fileName, base64_decode($image));

            EntregaFirma::create([
                'entrega_id' => $entrega->id,
                'archivo' => $fileName,
                'fecha_firma' => now(),
            ]);

            $entrega->update(['estado' => 'finalizada']);

            DB::commit();

            $this->reset(['firma_base64', 'entrega_id', 'detalleEntrega']);
            $this->loadEntregas();

            $this->dispatch('toast', type: 'success', message: 'Entrega firmada correctamente.');

        } catch (\Exception $e) {

            DB::rollBack();
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.mis-entregas')
            ->title('Mis Entregas');
    }
}
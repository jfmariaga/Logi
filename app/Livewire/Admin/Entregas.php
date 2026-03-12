<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Entrega;
use App\Models\EntregaItem;
use App\Models\Inventario;
use App\Models\User;
use App\Models\Producto;
use App\Models\ResponsableFirma;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class Entregas extends Component
{
    public $usuarios = [];
    public $productos = [];

    public $user_id;
    public $tipo = 'epp';
    public $observaciones;
    public $items = [];

    public $editing = false;
    public $entrega_id = null;
    public $firma_base64 = null;
    public $tieneFirma = null;

    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function mount()
    {
        $this->usuarios = User::orderBy('name')->get();
        $this->productos = Producto::where('activo', 1)->orderBy('nombre')->get();
        $this->tieneFirma = ResponsableFirma::where('user_id', auth()->id())->exists();
    }

    /* ===================================== */
    /* GET ENTREGAS */
    /* ===================================== */

    public function getEntregas()
    {
        $this->skipRender();

        return Entrega::with(['usuario', 'responsable', 'items.producto'])
            ->orderByDesc('id')
            ->get()
            ->map(function ($e) {

                $tipos = $e->items
                    ->pluck('producto.tipo')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                if (count($tipos) === 0) {
                    $tipoTexto = '-';
                } elseif (count($tipos) === 1) {
                    $tipoTexto = strtoupper($tipos[0]);
                } else {
                    $tipoTexto = 'EPP + DOTACION';
                }

                return [
                    'id' => $e->id,
                    'user_id' => $e->user_id,
                    'observaciones' => $e->observaciones,
                    'usuario' => $e->usuario?->name ?? '-',
                    'responsable' => $e->responsable?->name ?? '-',
                    'tipo' => $tipoTexto,
                    'estado' => $e->estado,
                    'items_count' => $e->items->count(),
                    'fecha' => $e->created_at->format('d/m/Y'),

                    'items' => $e->items->map(function ($i) {
                        return [
                            'producto_id' => $i->producto_id,
                            'cantidad' => $i->cantidad
                        ];
                    })->toArray()
                ];
            })
            ->toArray();
    }

    /* ===================================== */
    /* ITEMS */
    /* ===================================== */

    public function addItem()
    {
        $this->items[] = [
            'producto_id' => null,
            'cantidad' => 1,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    /* ===================================== */
    /* SAVE */
    /* ===================================== */

    public function save()
    {
        $this->validate();

        if (count($this->items) === 0) {
            $this->dispatch('toast', type: 'error', message: 'Debe agregar al menos un item.');
            return false;
        }

        DB::beginTransaction();

        try {

            $acumulados = [];

            foreach ($this->items as $item) {

                if (!$item['producto_id'] || $item['cantidad'] <= 0) {
                    throw new \Exception('Hay líneas incompletas.');
                }

                if (!isset($acumulados[$item['producto_id']])) {
                    $acumulados[$item['producto_id']] = 0;
                }

                $acumulados[$item['producto_id']] += (int)$item['cantidad'];
            }

            foreach ($acumulados as $producto_id => $cantidadTotal) {

                $producto = Producto::findOrFail($producto_id);

                $inventario = Inventario::where('producto_id', $producto->id)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario) {
                    throw new \Exception("No existe inventario configurado para {$producto->nombre}.");
                }

                $disponible = $inventario->cantidad - $inventario->cantidad_reservada;

                if ($cantidadTotal > $disponible) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}. Disponible: {$disponible}");
                }
            }

            if ($this->editing) {

                $entrega = Entrega::with('items')->findOrFail($this->entrega_id);

                foreach ($entrega->items as $oldItem) {
                    $inventario = Inventario::where('producto_id', $oldItem->producto_id)
                        ->lockForUpdate()
                        ->first();

                    if ($inventario) {
                        $inventario->decrement('cantidad_reservada', $oldItem->cantidad);
                    }
                }

                $entrega->items()->delete();

                $entrega->update([
                    'user_id' => $this->user_id,
                    'observaciones' => $this->observaciones,
                ]);
            } else {

                $entrega = Entrega::create([
                    'user_id' => $this->user_id,
                    'responsable_id' => auth()->id(),
                    'tipo' => $this->tipo,
                    'observaciones' => $this->observaciones,
                    'estado' => 'pendiente_firma'
                ]);
            }

            foreach ($this->items as $item) {

                $inventario = Inventario::where('producto_id', $item['producto_id'])
                    ->lockForUpdate()
                    ->first();

                $inventario->increment('cantidad_reservada', $item['cantidad']);

                EntregaItem::create([
                    'entrega_id' => $entrega->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                ]);
            }

            DB::commit();

            $this->limpiar();

            return Entrega::with(['usuario', 'responsable', 'items.producto'])
                ->where('id', $entrega->id)
                ->get()
                ->map(function ($e) {

                    $tipos = $e->items
                        ->pluck('producto.tipo')
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();

                    if (count($tipos) === 0) {
                        $tipoTexto = '-';
                    } elseif (count($tipos) === 1) {
                        $tipoTexto = strtoupper($tipos[0]);
                    } else {
                        $tipoTexto = 'EPP + DOTACION';
                    }

                    return [
                        'id' => $e->id,
                        'user_id' => $e->user_id,
                        'observaciones' => $e->observaciones,
                        'usuario' => $e->usuario?->name ?? '-',
                        'responsable' => $e->responsable?->name ?? '-',
                        'tipo' => $tipoTexto,
                        'estado' => $e->estado,
                        'items_count' => $e->items->count(),
                        'fecha' => $e->created_at->format('d/m/Y'),

                        'items' => $e->items->map(function ($i) {
                            return [
                                'producto_id' => $i->producto_id,
                                'cantidad' => $i->cantidad
                            ];
                        })->toArray()
                    ];
                })
                ->first();
        } catch (\Exception $e) {

            DB::rollBack();
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
            return false;
        }
    }


    public function firmar_responsable()
    {
        if (!$this->firma_base64) {
            $this->dispatch('toast', type: 'error', message: 'Debe realizar la firma.');
            return;
        }

        DB::beginTransaction();

        try {

            $image = str_replace('data:image/png;base64,', '', $this->firma_base64);
            $image = str_replace(' ', '+', $image);

            $fileName = 'firmas/responsable_' . auth()->id() . '_' . time() . '.png';

            Storage::disk('public')->put($fileName, base64_decode($image));

            ResponsableFirma::updateOrCreate(
                ['user_id' => auth()->id()],
                ['archivo' => $fileName]
            );

            DB::commit();

            $this->reset(['firma_base64']);
            $this->dispatch('toast', type: 'success', message: 'Firma registrada correctamente.');
            $this->mount();
        } catch (\Exception $e) {

            DB::rollBack();
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
        }
    }

    public function limpiar()
    {
        $this->reset([
            'user_id',
            'observaciones',
            'items',
            'editing',
            'entrega_id'
        ]);
    }

    public function generarPdf($id)
    {
        $entrega = Entrega::with([
            'usuario',
            'responsable',
            'items.producto',
            'firma'
        ])->findOrFail($id);

        $firmaEmpleado = $entrega->firma?->archivo;

        $firmaResponsable = ResponsableFirma::where(
            'user_id',
            $entrega->responsable_id
        )->first()?->archivo;

        $pdf = Pdf::loadView('pdf.entrega-epp', [
            'entrega' => $entrega,
            'firmaEmpleado' => $firmaEmpleado,
            'firmaResponsable' => $firmaResponsable,
        ])->setPaper('A4', 'portrait');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "Entrega_EPP_{$entrega->id}.pdf"
        );
    }

    public function render()
    {
        return view('livewire.admin.entregas')->title('Entregas');
    }
}

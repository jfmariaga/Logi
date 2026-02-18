<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Inventario;
use App\Models\InventarioMovimiento;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;


class Inventarios extends Component
{
    public $inventario_id;
    public $producto_id;
    public $talla;
    public $cantidad;
    public $modo = 'sumar';


    protected function rules()
    {
        return [
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ];
    }

    public function getInventarios()
    {
        $this->skipRender();

        return Inventario::with(['producto', 'movimientos.usuario'])
            ->orderByDesc('id')
            ->get()
            ->map(function ($i) {

                return [
                    'id' => $i->id,
                    'producto_id' => $i->producto_id,
                    'producto' => $i->producto->nombre,
                    'requiere_talla' => $i->producto->requiere_talla,
                    'talla' => $i->producto->talla,
                    'cantidad' => $i->cantidad,

                    // AQUI VA EL HISTORIAL
                    'movimientos' => $i->movimientos->map(function ($m) {
                        return [
                            'fecha' => $m->created_at->format('Y-m-d H:i'),
                            'usuario' => $m->usuario->name ?? 'Sistema',
                            'tipo' => $m->tipo,
                            'antes' => $m->cantidad_anterior,
                            'movimiento' => $m->cantidad_movimiento,
                            'despues' => $m->cantidad_nueva,
                            'descripcion' => $m->descripcion
                        ];
                    })
                ];
            })->toArray();
    }

    // ESTE ES EL IMPORTANTE
    public function getProductosSelect()
    {
        return Producto::where('activo', 1)->get()->map(function ($p) {

            if (!$p->requiere_talla) {
                return [
                    'producto_id' => $p->id,
                    'label' => $p->nombre,
                    'talla' => null
                ];
            }

            return [
                'producto_id' => $p->id,
                'label' => $p->nombre . ' - ' . $p->talla,
                'talla' => $p->talla
            ];
        })->values();
    }

    public function getMovimientosInventario($inventario_id)
    {
        return InventarioMovimiento::with('usuario')
            ->where('inventario_id', $inventario_id)
            ->latest()
            ->get()
            ->map(function ($m) {
                return [
                    'fecha' => $m->created_at->format('Y-m-d H:i'),
                    'usuario' => $m->usuario->name ?? 'Sistema',
                    'tipo' => $m->tipo,
                    'antes' => $m->cantidad_anterior,
                    'movimiento' => $m->cantidad_movimiento,
                    'despues' => $m->cantidad_nueva,
                    'descripcion' => $m->descripcion,
                ];
            })->toArray();
    }


    // public function save()
    // {
    //     $this->validate();

    //     //  migración automática: producto ahora usa talla pero existía sin talla
    //     if ($this->talla !== null) {

    //         $sinTalla = Inventario::where('producto_id', $this->producto_id)
    //             ->whereNull('talla')
    //             ->first();

    //         if ($sinTalla) {

    //             // mover stock a la nueva talla
    //             $inventario = Inventario::firstOrCreate([
    //                 'producto_id' => $this->producto_id,
    //                 'talla' => $this->talla
    //             ], [
    //                 'cantidad' => 0
    //             ]);

    //             $inventario->cantidad += $sinTalla->cantidad;
    //             $inventario->save();

    //             // registrar movimiento de migración
    //             InventarioMovimiento::create([
    //                 'inventario_id' => $inventario->id,
    //                 'producto_id' => $this->producto_id,
    //                 'user_id' => Auth::id(),
    //                 'tipo' => 'ajuste',
    //                 'cantidad_anterior' => 0,
    //                 'cantidad_movimiento' => $sinTalla->cantidad,
    //                 'cantidad_nueva' => $inventario->cantidad,
    //                 'descripcion' => 'Migración automática: producto ahora maneja talla'
    //             ]);

    //             $sinTalla->delete();
    //         }
    //     }

    //     $inventario = Inventario::where('producto_id', $this->producto_id)
    //         ->where('talla', $this->talla)
    //         ->first();

    //     if (!$inventario) {
    //         $inventario = Inventario::create([
    //             'producto_id' => $this->producto_id,
    //             'talla' => $this->talla,
    //             'cantidad' => 0,
    //         ]);
    //     }

    //     $antes = $inventario->cantidad;

    //     if ($this->modo === 'ajustar') {
    //         $inventario->cantidad = $this->cantidad;
    //         $movimiento = $inventario->cantidad - $antes;
    //         $tipo = 'ajuste';
    //     } else {
    //         $inventario->cantidad += $this->cantidad;
    //         $movimiento = $this->cantidad;
    //         $tipo = 'entrada';
    //     }

    //     $inventario->save();

    //     InventarioMovimiento::create([
    //         'inventario_id' => $inventario->id,
    //         'producto_id' => $this->producto_id,
    //         'user_id' => Auth::id(),
    //         'tipo' => $tipo,
    //         'cantidad_anterior' => $antes,
    //         'cantidad_movimiento' => $movimiento,
    //         'cantidad_nueva' => $inventario->cantidad,
    //         'descripcion' => $this->modo === 'ajustar'
    //             ? 'Ajuste manual de inventario'
    //             : 'Ingreso de stock',
    //     ]);

    //     $inventario->load('producto');

    //     $this->limpiar();

    //     return [
    //         'id' => $inventario->id,
    //         'producto_id' => $inventario->producto_id,
    //         'producto' => $inventario->producto->nombre,
    //         'requiere_talla' => $inventario->producto->requiere_talla,
    //         'talla' => $inventario->talla,
    //         'cantidad' => $inventario->cantidad,
    //     ];
    // }

    public function save()
    {
        $this->validate();

        // buscar SOLO por producto
        $inventario = Inventario::firstOrCreate(
            ['producto_id' => $this->producto_id],
            ['cantidad' => 0]
        );

        $antes = $inventario->cantidad;

        if ($this->modo === 'ajustar') {
            $inventario->cantidad = $this->cantidad;
            $movimiento = $inventario->cantidad - $antes;
            $tipo = 'ajuste';
        } else {
            $inventario->cantidad += $this->cantidad;
            $movimiento = $this->cantidad;
            $tipo = 'entrada';
        }

        $inventario->save();

        InventarioMovimiento::create([
            'inventario_id' => $inventario->id,
            'producto_id' => $inventario->producto_id,
            'user_id' => Auth::id(),
            'tipo' => $tipo,
            'cantidad_anterior' => $antes,
            'cantidad_movimiento' => $movimiento,
            'cantidad_nueva' => $inventario->cantidad,
            'descripcion' => $this->modo === 'ajustar'
                ? 'Ajuste manual de inventario'
                : 'Ingreso de stock',
        ]);

        $inventario->load('producto');

        $this->limpiar();

        return [
            'id' => $inventario->id,
            'producto_id' => $inventario->producto_id,
            'producto' => $inventario->producto->nombre,
            'requiere_talla' => $inventario->producto->requiere_talla,
            'talla' => $inventario->producto->talla, // <- ahora viene del producto
            'cantidad' => $inventario->cantidad,
        ];
    }


    public function limpiar()
    {
        $this->reset();
        $this->modo = 'sumar';
        $this->resetValidation();

        $this->dispatch('reset-inventario-form');
    }


    public function render()
    {
        return view('livewire.admin.inventarios', [
            'productosSelect' => $this->getProductosSelect()
        ])->title('Inventario');
    }
}

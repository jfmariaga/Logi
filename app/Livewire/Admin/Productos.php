<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Producto;

class Productos extends Component
{
    public $producto_id;
    public $nombre;
    public $tipo;
    public $referencia;
    public $descripcion;
    public $requiere_talla;
    public $talla;
    public $activo;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:epp,dotacion',
            'referencia' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'requiere_talla' => 'boolean',
            'talla' => 'nullable|string|max:50',
            'activo' => 'boolean',
        ];
    }

    public function getProductos()
    {
        $this->skipRender();

        return Producto::orderByDesc('id')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'tipo' => $p->tipo,
                'referencia' => $p->referencia,
                'descripcion' => $p->descripcion,
                'requiere_talla' => $p->requiere_talla,
                'talla' => $p->talla,
                'activo' => $p->activo,
            ];
        })->toArray();
    }

    public function save()
    {
        $this->validate();

        if (!$this->requiere_talla) {
            $this->talla = null;
        }

        if ($this->producto_id) {
            $producto = Producto::find($this->producto_id);

            if ($producto) {
                $producto->update([
                    'nombre' => $this->nombre,
                    'tipo' => $this->tipo,
                    'referencia' => $this->referencia,
                    'descripcion' => $this->descripcion,
                    'requiere_talla' => $this->requiere_talla,
                    'talla' => $this->talla,
                    'activo' => $this->activo,
                ]);
            }
        } else {
            $producto = Producto::create([
                'nombre' => $this->nombre,
                'tipo' => $this->tipo,
                'referencia' => $this->referencia,
                'descripcion' => $this->descripcion,
                'requiere_talla' => $this->requiere_talla,
                'talla' => $this->talla,
                'activo' => $this->activo,
            ]);
        }


        if ($producto) {
            $this->limpiar();
            return $producto->toArray();
        }

        return false;
    }

    public function desactivar($id)
    {
        $p = Producto::find($id);

        if ($p) {
            $p->update(['activo' => !$p->activo]);
            return $p->toArray();
        }

        return false;
    }

    public function limpiar()
    {
        $this->reset();
        $this->activo = true;
        $this->requiere_talla = false;

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.productos')
            ->title('Productos');
    }
}

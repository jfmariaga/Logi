<x-modal id="form_inventarios">

    <x-slot name="title">
        Ajustar Inventario
    </x-slot>

    <div class="row">

        <div class="col-md-12 mt-1">
            <x-select model="$wire.modo" label="Tipo de operación" id="modo">
                <option value="sumar">➕ Ingresar stock</option>
                <option value="ajustar">🛠 Ajustar cantidad real</option>
            </x-select>
        </div>


        <div class="col-md-12 mt-1">
            <x-select model="$wire.producto_id" label="Producto" id="producto">

                <option value="">Seleccione</option>

                @foreach ($productosSelect as $p)
                    <option value="{{ $p['producto_id'] }}" data-talla="{{ $p['talla'] }}">
                        {{ $p['label'] }}
                    </option>
                @endforeach

            </x-select>

        </div>

        <div class="col-md-12 mt-2">
            <x-input type="number" model="$wire.cantidad"
                label="{{ $modo == 'ajustar' ? 'Cantidad real en bodega' : 'Cantidad a ingresar' }}" />
        </div>

    </div>

    <x-slot name="footer">
        <div x-show="!loading_form">
            <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-outline-primary" x-on:click="saveFront()">Guardar</button>
        </div>

        <div x-show="loading_form">
            <x-spinner></x-spinner>
        </div>
    </x-slot>

</x-modal>

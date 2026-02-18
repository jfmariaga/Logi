<x-modal id="form_productos">

    <x-slot name="title">
        <span x-show="!$wire.producto_id">Crear producto</span>
        <span x-show="$wire.producto_id">Editar producto</span>
    </x-slot>

    <div class="row">

        <div class="col-md-6 mt-1">
            <x-input model="$wire.nombre" label="Nombre del producto" required="true" />
        </div>

        <div class="col-md-3 mt-1">
            <x-select model="$wire.tipo" label="Tipo" required="true" id="tipo">
                <option value="">Seleccione</option>
                <option value="epp">EPP</option>
                <option value="dotacion">Dotación</option>
            </x-select>
        </div>

        <div class="col-md-3 mt-1">
            <x-input model="$wire.referencia" label="Referencia" />
        </div>

        <div class="col-md-12 mt-1">
            <x-input type="textarea" model="$wire.descripcion" label="Descripción" />
        </div>

        <div class="col-md-6 mt-1">
            <x-select model="$wire.requiere_talla" label="¿Requiere talla?" required="true" id="requiere_talla">
                <option value="">Seleccione</option>
                <option value="0">No</option>
                <option value="1">Sí</option>
            </x-select>
        </div>

        {{-- <div class="col-md-6 mt-1" x-show="$wire.requiere_talla == 1">
            <x-input model="$wire.talla" label="Talla" />
        </div> --}}

        <div class="col-md-6 mt-1" x-show="@this.requiere_talla == 1">
            <x-input model="$wire.talla" label="Talla" />
        </div>

        <div class="col-md-6 mt-1">
            <x-select model="$wire.activo" label="Estado" required="true" id="activo">
                <option value="">Seleccione</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </x-select>
        </div>

    </div>

    <x-slot name="footer">
        <span>
            <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal" wire:click="limpiar">
                Cancelar
            </button>

            <button type="button" class="btn btn-outline-primary" x-on:click="saveFront()">
                Guardar
            </button>
        </span>
    </x-slot>

</x-modal>

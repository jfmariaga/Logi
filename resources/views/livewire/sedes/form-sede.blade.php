<x-modal id="form_sede">
    <x-slot name="title">
        <span x-show="!$wire.form_old">Agregar Sede</span>
        <span x-show="$wire.form_old">Editar Sede</span>
    </x-slot>

    <div class="row">
        <div class="col-md-12 mt-1">
            <x-input model="$wire.form.nombre" label="Nombre" required="true"></x-input>
        </div>
        <div class="col-md-12 mt-1">
            <x-input model="$wire.form.direccion" label="Dirección" required="true"></x-input>
        </div>
        <div class="col-md-6 mt-1">
            <x-input model="$wire.form.contacto" label="Contacto"></x-input>
        </div>
        <div class="col-md-6 mt-1">
            <x-input model="$wire.form.telefono_contacto" label="Teléfono contacto"></x-input>
        </div>
        <div class="col-md-6 mt-1">
            <x-input model="$wire.form.latitud" type="number" label="Latitud" required="true"></x-input>
        </div>
        <div class="col-md-6 mt-1">
            <x-input model="$wire.form.longitud" type="number" label="Longitud" required="true"></x-input>
        </div>
        <div class="col-md-6 mt-1">
            <x-input model="$wire.form.radio_metros" type="number" label="Radio metros" placeholder="150 por defecto..."></x-input>
        </div>

        <div class="col-md-6 mt-1" x-show="$wire.form_old">
            <x-select model="$wire.form.activo" label="Estado" id="activo" no_search="Infinity">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </x-select>
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

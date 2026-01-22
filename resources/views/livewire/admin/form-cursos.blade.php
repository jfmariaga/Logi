<x-modal id="form_cursos">

    <x-slot name="title">
        <span x-show="!$wire.curso_id">Crear curso</span>
        <span x-show="$wire.curso_id">Editar curso</span>
    </x-slot>

    <div class="row">

        <div class="col-md-12 mt-1">
            <x-input model="$wire.titulo" label="Título del curso" required="true"></x-input>
        </div>

        <div class="col-md-12 mt-1">
            <x-input type="textarea" model="$wire.descripcion" label="Descripción"></x-input>
        </div>

        <div class="col-md-6 mt-1">
            <x-input type="date" model="$wire.fecha_inicio" label="Fecha inicio"></x-input>
        </div>

        <div class="col-md-6 mt-1">
            <x-input type="date" model="$wire.fecha_fin" label="Fecha fin"></x-input>
        </div>
        <div class="col-md-6 mt-1">
            <x-input type="number" model="$wire.max_intentos" label="Máx. intentos" required />
        </div>

        <div class="col-md-6 mt-1">
            <x-input type="number" model="$wire.tiempo_minutos" label="Tiempo del examen (min)" />
        </div>

        <div class="col-md-6 mt-1">
            <x-input type="number" step="0.1" model="$wire.nota_minima" label="Nota mínima" />
        </div>

        <div class="col-md-6 mt-1">
            <x-select model="$wire.activo" label="Estado" required="true" id="activo">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </x-select>
        </div>

    </div>

    <x-slot name="footer">
        <span>
            <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">
                Cancelar
            </button>

            <button type="button" class="btn btn-outline-primary" x-on:click="saveFront()">
                Guardar
            </button>
        </span>
    </x-slot>

</x-modal>

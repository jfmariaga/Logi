<x-modal id="form_materiales">

    <div x-data="{ tipo: @entangle('tipo').defer }" x-cloak>

        <x-slot name="title">
            <span x-show="!$wire.material_id">Agregar material</span>
            <span x-show="$wire.material_id">Editar material</span>
        </x-slot>

        <div class="row">

            {{-- TIPO --}}
            <div class="col-md-12 mt-1">
                <label class="form-label">Tipo de material *</label>

                <select class="form-control" wire:model.defer="tipo" x-model="tipo">
                    <option value="">-- Seleccionar --</option>
                    <option value="video">Video</option>
                    <option value="pdf">PDF</option>
                    <option value="ppt">PowerPoint</option>
                    <option value="link">Enlace</option>
                </select>

                @error('tipo')
                    <span class="c_red">{{ $message }}</span>
                @enderror
            </div>

            {{-- TITULO --}}
            <div class="col-md-12 mt-1">
                <x-input model="$wire.titulo" label="Título" required="true"></x-input>
                @error('titulo')
                    <span class="c_red">{{ $message }}</span>
                @enderror
            </div>

            {{-- ARCHIVO --}}
            <div class="col-md-12 mt-1" x-show="['pdf','ppt','video'].includes(tipo)">
                <label class="form-label">Archivo</label>
                <input type="file" class="form-control" wire:model="archivo">

                @error('archivo')
                    <span class="c_red">{{ $message }}</span>
                @enderror

                <div wire:loading wire:target="archivo" class="text-info mt-1">
                    Subiendo archivo...
                </div>
            </div>

            {{-- LINK --}}
            <div class="col-md-12 mt-1" x-show="tipo === 'link'">
                <x-input model="$wire.url" label="URL del material"></x-input>
                @error('url')
                    <span class="c_red">{{ $message }}</span>
                @enderror
            </div>

            {{-- ORDEN --}}
            <div class="col-md-6 mt-1">
                <x-input type="number" model="$wire.orden" label="Orden" required="true"></x-input>
                @error('orden')
                    <span class="c_red">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <x-slot name="footer">
            <span>
                <button type="button" class="btn grey btn-outline-secondary"
                    x-on:click="
                                    @this.limpiar();
                                    $('#form_materiales').modal('hide');
                                ">
                    Cancelar
                </button>


                <button type="button" class="btn btn-outline-primary" x-on:click="saveFront()">
                    Guardar
                </button>
            </span>
        </x-slot>

    </div>

</x-modal>

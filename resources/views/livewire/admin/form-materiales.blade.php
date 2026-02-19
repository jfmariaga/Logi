<x-modal id="form_materiales">

    <div x-data="{ tipo: @entangle('tipo') }" x-cloak>

        <x-slot name="title">
            <span x-show="!$wire.material_id">Agregar material</span>
            <span x-show="$wire.material_id">Editar material</span>
        </x-slot>

        <div class="row">

            {{-- TIPO --}}
            <div class="col-md-12 mt-1">
                <label class="form-label">Tipo de material *</label>

                <select class="form-control" wire:model="tipo" x-model="tipo">
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
                <x-input model="$wire.titulo" label="Título" required="true" />
                @error('titulo')
                    <span class="c_red">{{ $message }}</span>
                @enderror
            </div>

            {{-- ======================= VIDEO (SUBIDA GRANDE) ======================= --}}
            <div class="col-md-12 mt-1" x-show="tipo === 'video'">

                <label class="form-label">
                    Video
                    @if ($material_id)
                        <small class="text-muted">(sube uno nuevo para reemplazar)</small>
                    @endif
                </label>

                <input type="file" id="videoUploader" class="form-control" accept="video/*">

                {{-- progreso --}}
                <div class="progress mt-2" style="height:8px">
                    <div id="videoProgress" class="progress-bar bg-success" style="width:0%"></div>
                </div>

                {{-- video actual --}}
                @if ($url)
                    <div class="mt-2 small">
                        <b>Video actual:</b><br>
                        <a href="{{ $url }}" target="_blank">🎬 Ver video cargado</a>
                    </div>
                @endif

                @error('archivo')
                    <span class="c_red">{{ $message }}</span>
                @enderror

                <small class="text-muted d-block mt-1">
                    El video se subirá antes de guardar el material
                </small>

            </div>
            <input type="hidden" wire:model="url" id="videoUrlHidden">

            {{-- ======================= PDF / PPT (LIVEWIRE NORMAL) ======================= --}}
            <div class="col-md-12 mt-1" x-show="['pdf','ppt'].includes(tipo)">

                <label class="form-label">
                    Archivo
                    @if ($material_id)
                        <small class="text-muted">(sube uno nuevo para reemplazar)</small>
                    @endif
                </label>

                <input type="file" class="form-control" wire:model="archivo">

                @if ($archivo_actual)
                    <div class="mt-2 small">
                        <b>Archivo actual:</b><br>
                        <a href="{{ Storage::url($archivo_actual) }}" target="_blank">
                            📎 Ver archivo cargado
                        </a>
                    </div>
                @endif

                @error('archivo')
                    <span class="c_red">{{ $message }}</span>
                @enderror

                <div wire:loading wire:target="archivo" class="text-info mt-1">
                    Subiendo archivo...
                </div>

            </div>


            {{-- LINK --}}
            <div class="col-md-12 mt-1" x-show="tipo === 'link'">
                <x-input model="$wire.url" label="URL del material" />
                @error('url')
                    <span class="c_red">{{ $message }}</span>
                @enderror
            </div>

            {{-- ORDEN --}}
            <div class="col-md-6 mt-1">
                <x-input type="number" model="$wire.orden" label="Orden" required="true" />
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

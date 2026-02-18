<div>

    <x-modal id="modal_config_notificaciones">

        <x-slot name="title">
            🔔 Configurar notificaciones
        </x-slot>

        <div class="p-2">

            {{-- INFORMACIÓN ACTUAL --}}
            <div class="alert alert-info mb-3">
                <b>Rol actual:</b> {{ $rolActual }} <br>
                <b>Última actualización:</b> {{ $ultimaActualizacion }}
            </div>

            <label class="mb-2">Nuevo rol receptor</label>

            <select wire:model="rolSeleccionado" class="form-control">
                <option value="">Seleccione un rol</option>

                @foreach ($roles as $rol)
                    <option value="{{ $rol }}">{{ $rol }}</option>
                @endforeach
            </select>

        </div>

        <x-slot name="footer">
            <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

            <button class="btn btn-primary" wire:click="guardar">
                Guardar configuración
            </button>
        </x-slot>

    </x-modal>


    @script
        <script>
            document.addEventListener('show-config-modal', () => {
                $('#modal_config_notificaciones').modal('show');
            });

            document.addEventListener('hide-config-modal', () => {
                $('#modal_config_notificaciones').modal('hide');
            });
        </script>
    @endscript

</div>

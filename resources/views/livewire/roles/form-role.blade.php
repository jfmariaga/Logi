<x-modal id="form_roles" size="lg">
    <x-slot name="title">
        <span x-show="!$wire.role_id">Agregar Rol</span>
        <span x-show="$wire.role_id">Editar Rol</span>
    </x-slot>

    <div class="row">
        <div class="col-md-12 mt-1">
            <x-input model="$wire.name" type="text" label="Nombre del Rol" required="true" />
        </div>

        <div class="col-md-12 mt-3">
            <label>Permisos</label>

            <div class="row">

                {{-- COLUMNA 1 --}}
                <div class="col-md-4">

                    <h6 class="text-uppercase text-primary">Dashboard</h6>
                    @foreach ($allPermissions->whereIn('name', ['ver dashboard','dashboard programación']) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Personal</h6>
                    @foreach ($allPermissions->whereIn('name', ['ver Sección personal']) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Empresas</h6>
                    @foreach ($allPermissions->whereIn('name', ['ver Sección Empresas']) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Página Web</h6>
                    @foreach ($allPermissions->whereIn('name', ['ver Sección página web']) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Marcaciones</h6>
                    @foreach ($allPermissions->whereIn('name', ['ver marcaciones']) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Programación</h6>
                    @foreach ($allPermissions->whereIn('name', [
                        'ver programación','crear programación','editar programación','eliminar programación'
                    ]) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                </div>

                {{-- COLUMNA 2 --}}
                <div class="col-md-4">

                    <h6 class="text-uppercase text-primary">Capacitaciones</h6>
                    @foreach ($allPermissions->whereIn('name', [
                        'ver capacitaciones','crear capacitaciones','editar capacitaciones','eliminar capacitaciones'
                    ]) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Cursos</h6>
                    @foreach ($allPermissions->whereIn('name', [
                        'ver materiales','ver preguntas','ver asignaciones','ver resultados','ver mis cursos'
                    ]) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Información de Interés</h6>
                    @foreach ($allPermissions->whereIn('name', [
                        'ver información de interes','crear información de interes',
                        'editar información de interes','eliminar información de interes'
                    ]) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Usuarios</h6>
                    @foreach ($allPermissions->whereIn('name', [
                        'ver usuarios','crear usuarios','editar usuarios','eliminar usuarios'
                    ]) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                </div>

                {{-- COLUMNA 3 --}}
                <div class="col-md-4">

                    <h6 class="text-uppercase text-primary">Sedes</h6>
                    @foreach ($allPermissions->whereIn('name', [
                        'ver sedes','crear sedes','editar sedes','eliminar sedes'
                    ]) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Gestión Documental</h6>
                    @foreach ($allPermissions->whereIn('name', [
                        'ver gestión documental','crear gestión documental',
                        'editar gestión documental','eliminar gestión documental'
                    ]) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Roles</h6>
                    @foreach ($allPermissions->whereIn('name', [
                        'ver roles','crear roles','editar roles','eliminar roles'
                    ]) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Debida Diligencia</h6>
                    @foreach ($allPermissions->whereIn('name', [
                        'ver listado','ver formularios','aprobar formularios','modificar notificaciones'
                    ]) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach

                    <h6 class="text-uppercase text-primary mt-3">Repositorio</h6>
                    @foreach ($allPermissions->whereIn('name', [
                        'ver repositorio','crear repositorio','editar repositorio','eliminar repositorio'
                    ]) as $p)
                        @include('admin.roles.checkbox', ['p' => $p])
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <x-slot name="footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
            Cancelar
        </button>
        <button type="button" class="btn btn-outline-primary" x-on:click="saveFront()">
            Guardar
        </button>
    </x-slot>
</x-modal>

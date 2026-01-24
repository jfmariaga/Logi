<div x-data="data_programacion">
     <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block br_none">Programación</h3>
                </div>
                <div class="content-header-right col-md-6 col-12">

                    @livewire('programacion.form-programacion')

                </div>
            </div>

            <div class="card m-0">
                <div class="card-content">
                    <div class="card-body">
                        <b class="mb-3">
                            Filtros
                        </b>
                        <hr>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="m-1">
                                    <x-input model="$wire.filtro_desde" type="date" label="Desde"></x-input>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="m-1">
                                    <x-input model="$wire.filtro_hasta" type="date" label="Hasta"></x-input>
                                </div>
                            </div>
                            <div class="col-md-3" wire:ignore>
                                <div class="m-1">
                                    <label for="filtro_programaciones">Sede específica</label>
                                    <select class="form-control select2" id="filtro_sede">
                                        <option @if( !$filtro_sede ) selected @endif value="0">Todas las sedes</option>
                                        @foreach ($sedes as $item)
                                            <option @if( $filtro_sede == $item->id ) selected @endif value="{{ $item->id }}">{{ $item->nombre }}</option> 
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" wire:ignore>
                                <div class="m-1">
                                    <label for="filtro_programaciones">Usuario específico</label>
                                    <select class="form-control select2" id="filtro_usuario">
                                        <option @if( !$filtro_usuario ) selected @endif value="0">Todos los usuarios</option>
                                        @foreach ($usuarios as $item)
                                            <option @if( $filtro_usuario == $item->id ) selected @endif value="{{ $item->id }}">{{ $item->name }} {{ $item->last_name }}</option> 
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="mt-1">
                                    <label for="">&nbsp;</label>
                                    <button class="btn btn-sm btn-light d-flex align-items-center justify-content-center" x-on:click="$wire.getProgramaciones()" title="Filtrar">
                                        <i class="la la-filter text-white"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- matriz inicial, fecha como key del grupo de programaciones --}}
            <template x-for="( data, fecha ) in getData">

                <div class="content-body">
                    <div class="card mb-1 mt-1 p-0">
                        <div class="card-header card-head-inverse bg_menu br_10" >
                            <h5 x-text="__formatDate(fecha)"></h5>
                        </div>
                    </div>

                    <div x-show=" data.operadores && Object.keys(data.operadores).length > 0" class="bs-callout-warning callout-border-left mt-1 p-1">
                        <strong>Operadores disponibles para este día!</strong>
                        <template x-for="operador in data.operadores">
                            <div>
                                <span x-text="`${operador.name} ${operador.last_name}`" class="ml-1"></span>
                                <span x-show="operador.phone" x-text="` - ${operador.phone}`"></span>
                            </div>
                        </template>
                    </div>

                    <div class="row">
                        <template x-for="programacion in data.programaciones">
                            <div class="col-md-12 p-1">
                                <div class="card border-left-blue border-right-blue m-0">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="f-right">
                                                <button type="button" x-on:click="confirmDelete( programacion.id )" class="btn btn-sm bg-danger white f-right ml-1"> 
                                                    <i class="la la-trash text-white"></i>
                                                </button>
                                                <button type="button" x-on:click="editProgramacion( programacion )" class="btn btn-sm btn-info f-right ml-1">
                                                    <i class="la la-edit text-white"></i>
                                                </button>
                                            </div>
                                            <h5 class=" mb-1">
                                                <b x-text="programacion.sede.nombre"></b>
                                            </h5>
                                            <div class="d-flex">
                                                <div class="w_140px">
                                                    <b>Dirección:</b>
                                                </div>
                                                <span x-text="programacion.sede.direccion"></span>
                                            </div>
                                            <div class="d-flex">
                                                <div class="w_140px">
                                                    <b>Hora de ingreso:</b>
                                                </div>
                                                <span x-text="__formatTime(programacion.hora_entrada)"></span>
                                            </div>
                                            <div class="d-flex">
                                                <div class="w_140px">
                                                    <b>Hora salida:</b>
                                                </div>
                                                <span x-text="__formatTime(programacion.hora_salida)"></span>
                                            </div>
                                            <div class="">
                                                <b>Personal:</b>
                                                <br>
                                                <div class="c_orange" x-show="programacion.personal.length == 0">
                                                    No se ha relacionado personal
                                                </div>
                                                <div class="heading-elements">
                                                    <div class="row">
                                                        <template x-for="usuario in programacion.personal">
                                                            <div class="col-md-4 mb-1">
                                                                <span>
                                                                    <img class="avatar_table" :src="`storage/avatars/${usuario.picture ?? 'default.png' }`" onerror="this.onerror=null;this.src='img/default.png';" alt="avatar">
                                                                </span>
                                                                <span x-text="`${usuario.name} ${usuario.last_name}`" class="ml-1"></span>
                                                                <span x-show="usuario.phone" x-text="` - ${usuario.phone}`"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

     @script
        <script>
            Alpine.data('data_programacion', () => ({

                init(){
                    $('#filtro_sede').change(() => {
                        @this.filtro_sede = $('#filtro_sede').val()
                    })
                    $('#filtro_usuario').change(() => {
                        @this.filtro_usuario = $('#filtro_usuario').val()
                    })
                },

                get getData(){
                    return @this.programaciones
                },

                editProgramacion( item_old = null ) {

                    Livewire.dispatch('edit_form_programacion', item_old)

                },

                confirmDelete(item_id) {
                    alertClickCallback('Eliminar',
                        'La programación será eliminada por completo, acción irreversible, desea continuar?',
                        'warning', 'Confirmar', 'Cancelar', async () => {
                            const res = await @this.eliminar(item_id);
                            if (res) {
                                toastRight('error', 'Acción realizada con éxito!');
                            }
                        });
                },


            }));
        </script>
    @endscript
</div>

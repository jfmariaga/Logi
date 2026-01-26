<div x-data="data_calendario">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/calendario.css') }}">

    {{-- <div class="content-header">
        <h4 class="content-header-title mb-0 d-inline-block br_none">Calendario</h4>
    </div> --}}

    <div class="calendario-container">
        @can('dashboard programación')
            <div class="row content_filtros_calendario">
                <div class="col-md-12">
                    <div class="col-md-12" wire:ignore>
                        <div class="d-flex">
                            <b class="mb-3">
                                Calendario
                            </b>
                            <div class="ml-auto">
                                @livewire('programacion.form-programacion')
                            </div>
                        </div>
                        <hr class="m-0">
                        <div class="row">
                            <div class="col-5 col-md-4">
                                <div class="m-1">
                                    <label for="filtro_programaciones">Mostrar por</label>
                                    <select class="form-control select2" id="filtro_tipo">
                                        <option @if ($filtro_tipo == '0') selected @endif value="0">Sedes
                                        </option>
                                        <option @if ($filtro_tipo == '1') selected @endif value="1">Usuarios
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-4" x-show="$wire.filtro_tipo == 0">
                                <div class="m-1">
                                    <label for="filtro_programaciones">Sede especifica</label>
                                    <select class="form-control select2" id="filtro_sede">
                                        <option @if (!$filtro_sede) selected @endif value="0">Todas las
                                            sedes</option>
                                        @foreach ($sedes as $item)
                                            <option @if ($filtro_sede == $item->id) selected @endif
                                                value="{{ $item->id }}">{{ $item->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-4" x-show="$wire.filtro_tipo == 1">
                                <div class="m-1">
                                    <label for="filtro_programaciones">Usuario especifico</label>
                                    <select class="form-control select2" id="filtro_usuario">
                                        <option @if (!$filtro_usuario) selected @endif value="0">Todos los
                                            usuarios</option>
                                        @foreach ($usuarios as $item)
                                            <option @if ($filtro_usuario == $item->id) selected @endif
                                                value="{{ $item->id }}">{{ $item->name }} {{ $item->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-1 col-md-1">
                                <div class="mt-1">
                                    <label for="">&nbsp;</label>
                                    <button class="btn btn-sm btn-light d-flex align-items-center justify-content-center"
                                        x-on:click="$wire.getProgramaciones()" title="Filtrar">
                                        <i class="la la-filter text-white"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        <!-- Botones para cambiar de mes -->
        <div class="mes-selector mt-3">
            <a class="btn btn-anterior" wire:click="mesAnterior">&lt; Mes Anterior</a>
            <h5><b class="mes-actual">{{ $mesesEnEspañol[date('F', mktime(0, 0, 0, $mesActual, 1))] }}
                    {{ $anoActual }}</b></h5>
            <a class="btn btn-siguiente" wire:click="mesSiguiente">Mes Siguiente &gt;</a>
        </div>

        <!-- Vista Calendario -->
        <table class="calendario">
            <thead>
                <tr class="dias-semana">
                    <th>Lun</th>
                    <th>Mar</th>
                    <th>Mié</th>
                    <th>Jue</th>
                    <th>Vie</th>
                    <th>Sáb</th>
                    <th>Dom</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($diasDelMes as $semana)
                    <tr class="dias">
                        @foreach ($semana as $dia)
                            @php
                                $fecha = $dia
                                    ? sprintf('%04d-%02d-%02d', (int) $anoActual, (int) $mesActual, (int) $dia)
                                    : null;
                                $esDiaYMesActual = $dia == now()->format('d') && $mesActual == now()->format('m');
                            @endphp
                            <td
                                class="dia-mes{{ $dia == '' ? ' disabled hide_responsive' : '' }} {{ $esDiaYMesActual ? 'dia-actual' : '' }}">
                                <div class="dia-numero d-flex">
                                    {{ $dia ? date('d', strtotime($fecha)) : '' }}
                                    <span
                                        class="show_responsive">&nbsp;-&nbsp;{{ $diasEnEspañol[date('D', strtotime($fecha))] ?? '' }}
                                    </span>
                                </div>

                                <div onclick="showXFecha( '{{ $fecha }}' )" data-toggle="modal"
                                    data-target="#showEventosXFecha" class="content_dia_calendario scroll_x w-100">
                                    <div>

                                        {{-- vista por sedes --}}
                                        @if (isset($programaciones[$fecha]['programaciones']) && $filtro_tipo == 0)
                                            @foreach ($programaciones[$fecha]['programaciones'] as $programacion)
                                                <div class="item_calendario">
                                                    <b>{{ $programacion['sede']['nombre'] }}</b>
                                                    @if ($programacion['hora_entrada'])
                                                        <div>
                                                            {{ date('h:i A', strtotime($programacion['hora_entrada'])) }}
                                                            -
                                                            {{ date('h:i A', strtotime($programacion['hora_salida'])) }}
                                                        </div>
                                                    @else
                                                        <div>No se registró horario</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif

                                        {{-- vista por personal --}}
                                        @if (isset($programaciones[$fecha]['personal']) && $filtro_tipo == 1)
                                            @foreach ($programaciones[$fecha]['programaciones'] as $programacion)
                                                @if (isset($programacion['personal']))
                                                    @foreach ($programacion['personal'] as $persona)
                                                        <div class="item_calendario">
                                                            <b>{{ $persona['name'] }} {{ $persona['last_name'] }}</b>
                                                            <div> {{ $programacion['sede']['nombre'] }} </div>
                                                            @if ($programacion['hora_entrada'])
                                                                <div>
                                                                    {{ date('h:i A', strtotime($programacion['hora_entrada'])) }}
                                                                    -
                                                                    {{ date('h:i A', strtotime($programacion['hora_salida'])) }}
                                                                </div>
                                                            @else
                                                                <div>No se registró horario</div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif

                                    </div>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @script
        <script>
            Alpine.data('data_calendario', () => ({

                init() {
                    Livewire.on('unblockPage', fecha => {
                        unblockPage(100)
                    })

                    $('#filtro_tipo').change(() => {
                        @this.filtro_tipo = $('#filtro_tipo').val()
                    })
                    $('#filtro_sede').change(() => {
                        @this.filtro_sede = $('#filtro_sede').val()
                    })
                    $('#filtro_usuario').change(() => {
                        @this.filtro_usuario = $('#filtro_usuario').val()
                    })
                },
                openFormRecordatorio(fecha) {
                    Livewire.emit('obtenerFechaRecordatorio', fecha);
                },

                openFormProgramacion(fecha) {
                    Livewire.emit('obtenerFechaProgramacion', fecha);
                },

                showXFecha(fecha) {
                    // Livewire.emit('showEventosXFecha', fecha);
                    // console.log('entrando')
                },
            }));
        </script>
    @endscript
</div>

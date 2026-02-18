<div class="content-wrapper p-3">

    {{-- HEADER --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">📊 Dashboard de Asistencia</h3>

        <button class="btn btn-light shadow-sm" wire:click="refreshData" wire:loading.attr="disabled">
            <span wire:loading.remove>
                <i class="la la-refresh"></i> Actualizar
            </span>
            <span wire:loading>
                <i class="la la-spinner la-spin"></i> Actualizando...
            </span>
        </button>
    </div>

    {{-- KPI --}}
    <div class="row mb-4">

        <div class="col-6 col-md-3">
            <div class="m-1">
                <div class="card card-corp text-center h-100 mb-0">
                    <small class="text-muted">Trabajando</small>
                    <h2 class="text-corp">{{ $totalTrabajando }}</h2>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="m-1">
                <div class="card card-corp text-center h-100 mb-0">
                    <small class="text-muted">Sedes en operación</small>
                    <h2 class="text-corp">{{ $totalSedes }}</h2>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="m-1">
                <div class="card card-corp text-center h-100 mb-0">
                    <small class="text-muted">Fuera sede hoy</small>
                    <h2 class="text-warning">{{ $fueraHoy }}</h2>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="m-1">
                <div class="card card-corp text-center h-100 mb-0">
                    <small class="text-muted">Marcaciones hoy</small>
                    <h2 class="text-corp">{{ $marcacionesHoy }}</h2>
                </div>
            </div>
        </div>

    </div>

    {{-- POR SEDE --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header sede-header">
            🏢 Operarios por sede
        </div>


        <div class="card default-collapse collapse-icon accordion-icon-rotate" >
            @php $contador = 0; @endphp
            @foreach ($porSede as $sedeId => $lista)
                @php $sede = $lista->first()->sede; @endphp
                
                <div class="card-header pointer bg_menu br_10 mt-1 @if(($contador % 2) == 0) bg_grey_suave @else bg_grey @endif collapsed" data-toggle="collapse" href="#collapse{{ $sedeId }}" aria-expanded="false">
                    {{ $sede->nombre ?? 'Sin sede' }} <strong>( {{ $lista->count() }} Operarios )</strong>
                </div>
                <div id="collapse{{ $sedeId }}" role="tabpanel" class="card-collapse collapse" aria-expanded="true"  style="">                    <div class="card-content" >
                            <div class="card-body p-2">

                                @foreach ($lista as $j)
                                    <div class="d-flex justify-content-between border-bottom py-2">

                                        <div>
                                            <b>{{ $j->user->name }} {{ $j->user->last_name }}</b><br>
                                            <small class="text-muted">
                                                Entrada: {{ $j->inicio->format('H:i') }}
                                            </small>
                                        </div>

                                        <div class="text-right">
                                            <small>
                                                ⏱ {{ $j->inicio->diffForHumans(null, true) }}
                                            </small><br>

                                            @if ($j->fuera_sede)
                                                <span class="badge badge-fuera">Fuera</span>
                                            @else
                                                <span class="badge badge-sede">En sede</span>
                                            @endif
                                        </div>

                                    </div>
                                @endforeach

                            </div>
                    </div>
                </div>
                @php $contador++; @endphp
            @endforeach
		</div>

    </div>

    {{-- ALERTAS --}}
    @if ($alertasLargas->count() || $fueraSede->count() || $cambioSede->count())
        <div class="card alert-corp mb-4">
            <div class="bs-callout-warning callout-border-left mt-1 p-1" bis_skin_checked="1">
                <strong>⚠️ Alertas</strong>
            </div>

            <div class="card-body">
                @foreach ($alertasLargas as $j)
                    <div>⏰ Jornada larga: <b>{{ $j->user->name }} {{ $j->user->last_name }}</b></div>
                @endforeach

                @foreach ($fueraSede as $j)
                    <div>📍 Fuera sede: <b>{{ $j->user->name }} {{ $j->user->last_name }}</b></div>
                @endforeach
                @foreach ($cambioSede as $j)
                    <div>
                        🔁 Cambio de sede:
                        <b>{{ $j->user->name }}</b><br>
                        <small>
                            {{ $j->sede->nombre }} ➜ {{ $j->sedeSalida->nombre ?? 'N/A' }}
                        </small>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ===== REPORTE HORAS-HOMBRE ===== --}}
    <div class="accordion" id="accordionReportes">

        <div x-data="nominaTable" wire:ignore>
            <div class="card shadow-sm mb-4">
                <div class="card-header sede-header d-flex justify-content-between align-items-center">
                    <div class="row w-100">
                        <div class="col-md-3">
                            💼 Resumen Horas por empleado
                        </div>
                        <div class="col-md-9 row g-2 align-items-end">
                            <div class="col-6 col-md-3">
                                <input type="date" class="form-control form-control-sm" x-model="desde">
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="date" class="form-control form-control-sm" x-model="hasta">
                            </div>
                            <div class="col-6 col-md-3">
                                <select class="form-control form-control-sm" x-model="user_id">
                                    <option value="">Todos</option>
                                    @foreach ($empleados as $e)
                                        <option value="{{ $e->id }}">
                                            {{ $e->name }} {{ $e->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select class="form-control form-control-sm" x-model="sede_id">
                                    <option value="">Todas las sedes</option>
                                    @foreach ($sedes as $s)
                                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-1">
                                <button class="btn btn-sm btn-light w-100" @click="getData()" title="Filtrar">
                                    <i class="la la-filter"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div x-show="!loading">
                        <div x-show="!loading">
                            <x-table id="table_nomina">
                                <tr>
                                    <th>Empleado</th>
                                    <th>Fecha</th>
                                    <th>Sede</th>
                                    <th>Horas</th>
                                </tr>
                            </x-table>
                        </div>
                    </div>
                    <div x-show="loading">
                        <x-spinner />
                    </div>
                </div>
            </div>

            @script
                <script>
                    Alpine.data('nominaTable', () => ({

                        loading: true,
                        desde: null,
                        hasta: null,
                        user_id: '',
                        sede_id: '',

                        init() {
                            const hoy = new Date().toISOString().substr(0, 10);
                            this.desde = hoy;
                            this.hasta = hoy;
                            this.getData();
                        },

                        async getData() {
                            this.loading = true;
                            const filtros = {
                                desde: this.desde,
                                hasta: this.hasta,
                                user_id: this.user_id,
                                sede_id: this.sede_id,
                            };

                            const data = await @this.call('getNomina', filtros);

                            if ($.fn.DataTable.isDataTable('#table_nomina')) {
                                $('#table_nomina').DataTable().clear().destroy();
                            }

                            $('#body_table_nomina').html('');

                            for (const r of data) {
                                this.addRow(r);
                            }

                            setTimeout(() => {
                                initDataTableSpanish('#table_nomina', {
                                    order: [
                                        [0, 'asc']
                                    ],
                                    rowGroup: {
                                        dataSrc: 0,
                                        startRender: null,
                                        endRender: function(rows, group) {
                                            let total = rows
                                                .data()
                                                .pluck(3)
                                                .reduce((a, b) => a + parseFloat(b), 0);

                                            return $('<tr/>')
                                                .append(
                                                    '<td colspan="3" class="text-right font-weight-bold">Total ' +
                                                    group + '</td>'
                                                )
                                                .append(
                                                    '<td class="font-weight-bold">' + total.toFixed(
                                                        2) + '</td>'
                                                );
                                        }
                                    }
                                });

                                this.loading = false;

                            }, 150);
                        },

                        addRow(r) {
                            const tr = `
                                <tr>
                                    <td>${r.empleado}</td>
                                    <td>${r.fecha}</td>
                                    <td>${r.sede}</td>
                                    <td>${r.horas}</td>
                                </tr>`;

                            $('#body_table_nomina').append(tr);
                        }

                    }));
                </script>
            @endscript

        </div>

        {{-- ===== MARCACIONES RECIENTES ===== --}}
        <div x-data="marcacionesTable" wire:ignore>

            <div class="card">
                <div class="card-header sede-header d-flex justify-content-between align-items-center">
                    <div class="row w-100">
                        <div class="col-md-3">
                            🧾 Marcaciones
                        </div>
                        <div class="col-md-9 row g-2 ">
                            <div class="col-6 col-md-2">
                                <input type="date" class="form-control form-control-sm" x-model="desde">
                            </div>
                            <div class="col-6 col-md-2">
                                <input type="date" class="form-control form-control-sm" x-model="hasta">
                            </div>
                            <div class="col-6 col-md-3">
                                <select class="form-control form-control-sm" x-model="user_id">
                                    <option value="">Todos los operadores</option>
                                    @foreach ($empleados as $e)
                                        <option value="{{ $e->id }}">
                                            {{ $e->name }} {{ $e->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select class="form-control form-control-sm" x-model="sede_id">
                                    <option value="">Todas las sedes</option>
                                    @foreach ($sedes as $s)
                                        <option value="{{ $s->id }}">
                                            {{ $s->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select class="form-control form-control-sm" x-model="estado">
                                    <option value="">En sede y fuera de sede</option>
                                    <option value="1">En sede</option>
                                    <option value="0">Fuera</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-1 d-flex gap-1">
                                <button class="btn btn-sm btn-light w-100 d-flex align-items-center justify-content-center"
                                    @click="getData()" title="Filtrar">
                                    <i class="la la-filter"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <div x-show="!loading">
                        <x-table id="table_marcaciones">
                            <tr>
                                <th>Empleado</th>
                                <th>Sede</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Distancia</th>
                                <th>Estado</th>
                                <th>Selfie</th>
                            </tr>
                        </x-table>
                    </div>

                    <div x-show="loading">
                        <x-spinner />
                    </div>
                </div>
            </div>

            {{-- MODAL SELFIE --}}
            <x-modal id="modal_foto_marcacion" size="md">
                <x-slot name="title">📸 Selfie de Marcación</x-slot>

                <div class="text-center">
                    <img id="fotoMarcacionPreview" class="img-fluid rounded shadow mb-2">
                    <a id="downloadFoto" class="btn btn-sm btn-outline-primary" download>
                        Descargar
                    </a>
                </div>

                <x-slot name="footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </x-slot>
            </x-modal>

            @script
                <script>
                    Alpine.data('marcacionesTable', () => ({

                        loading: true,
                        desde: null,
                        hasta: null,
                        user_id: '',
                        sede_id: '',
                        estado: '',
                        rows: [],

                        init() {
                            const hoy = new Date().toISOString().substr(0, 10);
                            this.desde = hoy;
                            this.hasta = hoy;
                            this.getData();
                        },

                        async getData() {
                            this.loading = true;

                            const filtros = {
                                desde: this.desde,
                                hasta: this.hasta,
                                user_id: this.user_id,
                                sede_id: this.sede_id,
                                estado: this.estado,
                            };

                            this.rows = await @this.call('getMarcaciones', filtros);

                            if ($.fn.DataTable.isDataTable('#table_marcaciones')) {
                                $('#table_marcaciones').DataTable().clear().destroy();
                            }

                            $('#body_table_marcaciones').html('');

                            for (const r of this.rows) {
                                this.addRow(r);
                            }

                            setTimeout(() => {
                                initDataTableSpanish('#table_marcaciones', {
                                    order: [
                                        [3, 'desc']
                                    ]
                                });
                                this.loading = false;
                            }, 150);
                        },

                        addRow(r) {

                            let fotoHtml = r.foto ?
                                `<button class="btn btn-sm btn-outline-primary"
                            onclick="verFotoMarcacion('${r.foto}')">
                            <i class="la la-camera"></i>
                       </button>` :
                                '—';

                            const tr = `
                    <tr>
                        <td>${r.user}</td>
                        <td>${r.sede}</td>
                        <td>${r.tipo}</td>
                        <td>${r.fecha}</td>
                        <td>${r.distancia} m</td>
                        <td>${r.estado}</td>
                        <td class="text-center">${fotoHtml}</td>
                    </tr>`;

                            $('#body_table_marcaciones').append(tr);
                        }

                    }));
                </script>
            @endscript
        </div>
    </div>

    <script>
        function initDataTableSpanish(selector, extraOptions = {}) {

            const baseOptions = {
                dom: '<"row mb-2"<"col-md-6"B><"col-md-6 text-right"f>>' +
                    '<"row"<"col-md-12"tr>>' +
                    '<"row mt-2"<"col-md-5"i><"col-md-7 text-right"p>>',

                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Descargar Excel',
                    className: 'btn btn-outline-light btn-sm'
                }],

                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },

                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            };

            return $(selector).DataTable({
                ...baseOptions,
                ...extraOptions
            });
        }
    </script>
    {{-- JS GLOBAL PARA MODAL SELFIE --}}
    <script>
        function verFotoMarcacion(url) {
            document.getElementById('fotoMarcacionPreview').src = url;
            document.getElementById('downloadFoto').href = url;
            $('#modal_foto_marcacion').modal('show');
        }
    </script>

</div>

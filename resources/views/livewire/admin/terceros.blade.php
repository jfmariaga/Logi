<div>
    <div x-data="terceros">

        <div class="content-wrapper p-3">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>📄 Gestión de Terceros</h4>

                @can('modificar notificaciones')
                    <button class="btn btn-outline-dark" onclick="Livewire.dispatch('abrirConfigNotificaciones')">
                        ⚙️ Notificaciones
                    </button>
                @endcan
            </div>

            {{-- TARJETAS RESUMEN --}}
            <div class="row mb-4">

                <div class="col">
                    <div class="card p-3 text-center">
                        <strong>En proceso</strong>
                        <h4>{{ $resumen['en_proceso'] ?? 0 }}</h4>
                    </div>
                </div>

                <div class="col">
                    <div class="card p-3 text-center">
                        <strong>Pendientes</strong>
                        <h4>{{ $resumen['pendientes'] ?? 0 }}</h4>
                    </div>
                </div>

                <div class="col">
                    <div class="card p-3 text-center">
                        <strong>Aprobados</strong>
                        <h4>{{ $resumen['aprobados'] ?? 0 }}</h4>
                    </div>
                </div>

                <div class="col">
                    <div class="card p-3 text-center">
                        <strong>Rechazados</strong>
                        <h4>{{ $resumen['rechazados'] ?? 0 }}</h4>
                    </div>
                </div>

            </div>

            <div class="card">
                <div x-show="!loading">

                    <x-table id="table_terceros">
                        <tr>
                            <th>Identificación</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Progreso</th>
                            <th>Enviado</th>
                            <th>Actualizado</th>
                            <th>Acciones</th>
                        </tr>
                    </x-table>

                </div>

                <div x-show="loading">
                    <x-spinner />
                </div>
            </div>

            @livewire('admin.config-notificaciones-modal')

        </div>

        @script
            <script>
                Alpine.data('terceros', () => ({

                    terceros: [],
                    loading: true,

                    init() {
                        this.getTerceros();
                    },

                    async getTerceros() {
                        this.loading = true;

                        this.terceros = await @this.getTerceros();

                        for (const t of this.terceros) {
                            this.addTercero(t);
                        }

                        setTimeout(() => {
                            __resetTable('#table_terceros');
                            this.loading = false;
                        }, 400);
                    },

                    addTercero(t) {

                        let tr = `<tr id="tercero_${t.id}">
                        <td>${t.identificacion}</td>
                        <td>${t.nombre}</td>
                        <td>${t.tipo}</td>
                        <td>${t.estado}</td>
                        <td>${t.progreso}%</td>
                        <td>${t.enviado}</td>
                        <td>${t.actualizado}</td>
                        <td>
                            @can('ver formularios')
                                <div class="d-flex">
                                    <x-buttonsm click="goDetalle('${t.id}')" color="primary" title="Ver formulario">
                                        <i class="la la-eye"></i>
                                    </x-buttonsm>
                                </div>
                            @endcan
                        </td>
                    </tr>`;

                        $('#body_table_terceros').append(tr);
                        Alpine.initTree(document.getElementById('tercero_' + t.id));
                    },

                    goDetalle(id) {
                        location.href = `/admin/terceros/${id}/auditar`;
                    }

                }));
            </script>
        @endscript

    </div>
</div>

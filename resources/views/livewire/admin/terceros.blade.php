<div>
    <div x-data="terceros">

        <div class="content-wrapper p-3">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>📄 Asociados de negocio</h4>

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
                            <div class="d-flex">
                                @can('ver formularios')
                                    <x-buttonsm click="goDetalle('${t.id}')" color="primary" title="Ver formulario">
                                        <i class="la la-eye"></i>
                                    </x-buttonsm>
                                @endcan
                                @can('eliminar formularios')
                                    <x-buttonsm click="confirmDelete('${t.id}')" color="danger" title="Eliminar formulario" classextra="ml-1">
                                        <i class="la la-trash"></i>
                                    </x-buttonsm>
                                @endcan
                            </div>
                        </td>
                    </tr>`;

                        $('#body_table_terceros').append(tr);
                        Alpine.initTree(document.getElementById('tercero_' + t.id));
                    },

                    goDetalle(id) {
                        location.href = `/admin/terceros/${id}/auditar`;
                    },

                    confirmDelete(id) {
                        alertClickCallback('Eliminar',
                            'El formulario y todos sus datos, documentos y firmas se eliminarán por completo. Esta acción no se puede deshacer.',
                            'warning', 'Eliminar', 'Cancelar', async () => {
                                await @this.eliminarTercero(id);
                                $(`#tercero_${id}`).remove();
                                toastRight('error', 'Formulario eliminado');
                            });
                    }

                }));
            </script>
        @endscript

    </div>
</div>

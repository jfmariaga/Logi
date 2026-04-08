<div>
    <div x-data="resultados">

        <div class="content-wrapper p-3">

            <div class="d-flex justify-content-between mb-3">
                <h4>📊 Resultados — {{ $curso->titulo }}</h4>

                <a href="{{ route('cursos') }}" class="btn btn-secondary">
                    ← Volver
                </a>
            </div>

            {{-- TARJETAS RESUMEN --}}
            <div class="row mb-3">
                <template x-for="(v, k) in resumen" :key="k">
                    <div class="col-md-2 col-6 mb-2">
                        <div class="card text-center shadow-sm">
                            <div class="card-body p-2">
                                <small class="text-muted" x-text="labels[k]"></small>
                                <h4 class="mb-0" x-text="v"></h4>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- TABLA --}}
            <div class="card">
                <div x-show="!loading">
                    <x-table id="table_resultados">
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Intentos</th>
                            <th>Nota</th>
                            <th>Estado</th>
                            <th>Fecha último intento</th>
                            <th>Acc</th>
                        </tr>
                    </x-table>
                </div>

                <div x-show="loading">
                    <x-spinner />
                </div>
            </div>

        </div>

        {{-- MODAL AUDITORIA --}}
        <div class="modal fade" id="modal_auditoria_resultados" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-0">Auditoría de respuestas</h5>
                            <small class="text-muted" x-text="usuarioAuditadoNombre || '—'"></small>
                        </div>

                        <button type="button" class="close" @click="cerrarAuditoria()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div x-show="loadingAuditoria" class="text-center py-4">
                            <x-spinner />
                            <div class="mt-2">Cargando auditoría...</div>
                        </div>

                        <div x-show="!loadingAuditoria && auditoria.length === 0" class="alert alert-light border">
                            Este usuario no tiene intentos registrados.
                        </div>

                        <template x-for="intento in auditoria" :key="intento.id">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <strong x-text="'Intento #' + intento.intento_numero"></strong>
                                    </div>

                                    <div class="d-flex flex-wrap gap-3 small">
                                        <span class="mr-3">
                                            <strong>Inicio:</strong>
                                            <span x-text="intento.fecha_inicio ?? '—'"></span>
                                        </span>

                                        <span class="mr-3">
                                            <strong>Fin:</strong>
                                            <span x-text="intento.fecha_fin ?? '—'"></span>
                                        </span>

                                        <span class="mr-3">
                                            <strong>Nota:</strong>
                                            <span x-text="intento.nota ?? '—'"></span>
                                        </span>

                                        <span
                                            x-html="intento.aprobado
                                            ? '<span class=\'text-success font-weight-bold\'>Aprobado</span>'
                                            : '<span class=\'text-danger font-weight-bold\'>No aprobado</span>'">
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 60px;">#</th>
                                                    <th>Pregunta</th>
                                                    <th>Respuesta del usuario</th>
                                                    <th style="width: 120px;">Resultado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-if="intento.respuestas.length === 0">
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-3">
                                                            No hay respuestas registradas para este intento.
                                                        </td>
                                                    </tr>
                                                </template>

                                                <template x-for="(resp, index) in intento.respuestas"
                                                    :key="resp.id">
                                                    <tr>
                                                        <td class="text-center" x-text="index + 1"></td>
                                                        <td x-text="resp.pregunta"></td>
                                                        <td x-text="resp.respuesta_usuario"></td>
                                                        <td class="text-center">
                                                            <span
                                                                x-html="resp.es_correcta
                                                                ? '<span class=\'badge badge-success\'>Correcta</span>'
                                                                : '<span class=\'badge badge-danger\'>Incorrecta</span>'">
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarAuditoria()">
                            Cerrar
                        </button>
                    </div>

                </div>
            </div>
        </div>

        @script
            <script>
                Alpine.data('resultados', () => ({

                    data: [],
                    resumen: {},
                    loading: true,

                    auditoria: [],
                    loadingAuditoria: false,
                    usuarioAuditadoId: null,
                    usuarioAuditadoNombre: '',

                    labels: {
                        inscritos: '👥 Inscritos',
                        iniciaron: '▶️ Iniciaron',
                        pendientes: '⏳ Pendientes',
                        aprobados: '✅ Aprobados',
                        no_aprobados: '❌ No aprobados'
                    },

                    init() {
                        this.getResumen();
                        this.getResultados();
                    },

                    async getResumen() {
                        this.resumen = await @this.getResumen();
                    },

                    async getResultados() {
                        this.loading = true;

                        this.data = await @this.getResultados();

                        $('#body_table_resultados').html('');

                        for (const r of this.data) {
                            this.addRow(r);
                        }

                        setTimeout(() => {
                            __resetTable('#table_resultados');
                            this.loading = false;
                        }, 300);
                    },

                    estadoHtml(estado) {
                        if (estado === 'aprobado') {
                            return '<span class="text-success">Aprobado</span>';
                        } else if (estado === 'no_aprobado') {
                            return '<span class="text-danger">No aprobado</span>';
                        }

                        return '<span class="text-muted">Pendiente</span>';
                    },

                    escapeHtml(text) {
                        if (text === null || text === undefined) return '';
                        return String(text)
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#039;');
                    },

                    addRow(r) {
                        let tr = `
                            <tr id="res_${r.id}">
                                <td>${this.escapeHtml(r.nombre)}</td>
                                <td>${this.escapeHtml(r.rol || '—')}</td>
                                <td class="text-center">${r.intentos}</td>
                                <td class="text-center">${r.nota ?? '—'}</td>
                                <td class="text-center">${this.estadoHtml(r.estado)}</td>
                                <td>${this.escapeHtml(r.fecha ?? '—')}</td>
                                <td>
                                    <div class="d-flex">
                                        <x-buttonsm click="openAuditoria('${r.id}')">
                                            <i class="la la-search"></i>
                                        </x-buttonsm>
                                    </div>
                                </td>
                            </tr>
                        `;

                        let row = document.getElementById(`res_${r.id}`);

                        if (row) {
                            row.outerHTML = tr;
                        } else {
                            $('#body_table_resultados').prepend(tr);
                        }
                    },

                    async openAuditoria(userId) {
                        let u = this.data.find(x => x.id == userId) ?? {};

                        this.usuarioAuditadoId = userId;
                        this.usuarioAuditadoNombre = u.nombre ?? '';
                        this.auditoria = [];
                        this.loadingAuditoria = true;

                        $('#modal_auditoria_resultados').modal('show');

                        try {
                            this.auditoria = await @this.getAuditoriaUsuario(userId);
                        } catch (e) {
                            console.error(e);
                            this.auditoria = [];
                            toastRight('error', 'No se pudo cargar la auditoría');
                        } finally {
                            this.loadingAuditoria = false;
                        }
                    },

                    cerrarAuditoria() {
                        $('#modal_auditoria_resultados').modal('hide');
                    }

                }))
            </script>
        @endscript
    </div>
</div>

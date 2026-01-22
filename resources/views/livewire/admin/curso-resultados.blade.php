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
                        </tr>
                    </x-table>
                </div>

                <div x-show="loading">
                    <x-spinner />
                </div>
            </div>

        </div>

        @script
            <script>
                Alpine.data('resultados', () => ({

                    data: [],
                    resumen: {},
                    loading: true,

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

                        for (const r of this.data) {
                            this.addRow(r);
                        }

                        setTimeout(() => {
                            __resetTable('#table_resultados');
                            this.loading = false;
                        }, 300);
                    },

                    addRow(r) {

                        let estado = '—';

                        if (r.estado === 'aprobado') {
                            estado = '<span class="text-success">Aprobado</span>';
                        } else if (r.estado === 'no_aprobado') {
                            estado = '<span class="text-danger">No aprobado</span>';
                        } else {
                            estado = '<span class="text-muted">Pendiente</span>';
                        }


                        let tr = `
                            <tr id="res_${r.id}">
                                <td>${r.nombre}</td>
                                <td>${r.rol || '—'}</td>
                                <td class="text-center">${r.intentos}</td>
                                <td class="text-center">${r.nota ?? '—'}</td>
                                <td class="text-center">${estado}</td>
                                <td>${r.fecha ?? '—'}</td>
                            </tr>
                        `;

                        $('#body_table_resultados').prepend(tr);
                    }

                }))
            </script>
        @endscript

    </div>
</div>

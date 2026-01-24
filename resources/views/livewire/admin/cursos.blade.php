<div>
    <style>
        #table_cursos th:first-child,
        #table_cursos td:first-child {
            min-width: 320px;
            white-space: normal !important;
        }

        .stat-box {
            min-width: 90px;
            padding: 8px 10px;
            border-radius: 10px;
            text-align: center;
            line-height: 1.1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
            font-size: 13px;
        }

        .stat-num {
            font-size: 18px;
            font-weight: 700;
        }

        .stat-label {
            font-size: 11px;
            opacity: .9;
        }
    </style>
    <div x-data="cursos">
        <div class="content-wrapper p-3">
            <div class="d-flex justify-content-between mb-3">
                <h4>🎓 Cursos de Capacitación</h4>
                <button class="btn btn-dark" @click="openForm()">Nuevo</button>
            </div>
            <div class="card">
                <div x-show="!loading">
                    <x-table id="table_cursos">
                        <tr>
                            <th>Título</th>
                            <th>Nota mínima</th>
                            <th>Tiempo en minutos</th>
                            <th>Intentos</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                            <th>Acc</th>
                        </tr>
                    </x-table>
                </div>
                <div x-show="loading">
                    <x-spinner />
                </div>
            </div>
        </div>

        @include('livewire.admin.form-cursos')

        @script
            <script>
                Alpine.data('cursos', () => ({

                    cursos: [],
                    loading: true,

                    init() {
                        this.getCursos();

                        $('#activo').change(() => {
                            @this.activo = $('#activo').val()
                        })
                    },

                    async getCursos() {
                        this.loading = true;
                        this.cursos = await @this.getCursos();
                        for (const c of this.cursos) {
                            this.addCurso(c);
                        }

                        setTimeout(() => {
                            __resetTable('#table_cursos');
                            this.loading = false;
                        }, 400);
                    },

                    addCurso(curso, is_update = false) {
                        let tr = ``;
                        if (!is_update) tr += `<tr id="curso_${curso.id}">`;
                        tr += `
                            <td>
                                <a href="javascript:void(0)"
                                    class="font-weight-bold text-primary d-block"
                                    style="cursor:pointer; line-height:1.2"
                                    onclick="toggleDetalleCurso(${curso.id})">
                                        ${curso.titulo}
                                </a>
                                <div id="detalle_${curso.id}" style="display:none"
                                    class="mt-2 p-2 bg-light rounded small">

                                    <b>Descripción:</b><br>
                                    ${curso.descripcion ?? '—'}<br><br>

                                    <div class="d-flex flex-wrap gap-2 mt-2">

                                        <div class="stat-box bg-light border">
                                            <div class="stat-num text-dark">${curso.inscritos}</div>
                                            <div class="stat-label">👥 Inscritos</div>
                                        </div>

                                        <div class="stat-box bg-success text-white"
                                            style="cursor:pointer"
                                            @click="goResultadosFiltro(${curso.id}, 'aprobado')">
                                            <div class="stat-num">${curso.aprobados}</div>
                                            <div class="stat-label">✅ Aprobados</div>
                                        </div>

                                        <div class="stat-box bg-danger text-white"
                                            style="cursor:pointer"
                                            @click="goResultadosFiltro(${curso.id}, 'reprobado')">
                                            <div class="stat-num">${curso.reprobados}</div>
                                            <div class="stat-label">❌ Reprobados</div>
                                        </div>

                                        <div class="stat-box bg-warning text-dark"
                                            style="cursor:pointer"
                                            @click="goResultadosFiltro(${curso.id}, 'pendiente')">
                                            <div class="stat-num">${curso.faltan}</div>
                                            <div class="stat-label">⏳ Pendientes</div>
                                        </div>


                                    </div>


                                </div>
                            </td>
                            <td>${curso.nota_minima ?? '—'}</td>
                            <td>${curso.tiempo_minutos ?? '—'}</td>
                            <td>${curso.max_intentos ?? '—'}</td>
                            <td>${curso.fecha_inicio ?? '—'}</td>
                            <td>${curso.fecha_fin ?? '—'}</td>
                            <td>
                                ${curso.activo == 1
                                    ? '<span class="text-success">Activo</span>'
                                    : '<span class="text-danger">Inactivo</span>'}
                            </td>
                            <td>
                                <div class="d-flex">
                                    <x-buttonsm click="openForm('${curso.id}')" title="Editar curso">
                                        <i class="la la-edit"></i>
                                    </x-buttonsm>
                                    <x-buttonsm click="goMateriales('${curso.id}')" color="info" title="Materiales">
                                        <i class="la la-book"></i>
                                    </x-buttonsm>
                                    <x-buttonsm click="goPreguntas('${curso.id}')" color="warning" title="Preguntas">
                                        <i class="la la-question-circle"></i>
                                    </x-buttonsm>
                                    <x-buttonsm click="goAsignaciones('${curso.id}')" color="secondary" title="Asignaciones">
                                        <i class="la la-users"></i>
                                    </x-buttonsm>
                                    <x-buttonsm click="goResultados('${curso.id}')" color="success" title="Resultados">
                                        <i class="la la-bar-chart"></i>
                                    </x-buttonsm>
                                    <x-buttonsm click="confirmDelete('${curso.id}')" color="danger" title="Desactivar">
                                        <i class="la la-trash"></i>
                                    </x-buttonsm>
                                </div>
                            </td>
                        `;

                        if (!is_update) {
                            tr += `</tr>`;
                            $('#body_table_cursos').prepend(tr);
                            Alpine.initTree(document.getElementById('curso_' + curso.id));
                        } else {
                            $(`#curso_${curso.id}`).html(tr);
                            Alpine.initTree(document.getElementById('curso_' + curso.id));
                        }
                    },

                    async saveFront() {
                        const is_update = @this.curso_id ? true : false;
                        const curso = await @this.save();
                        if (curso) {
                            this.addCurso(curso, is_update);
                            $('#form_cursos').modal('hide');
                            toastRight('success', is_update ? 'Curso actualizado' : 'Curso creado');
                        }
                    },

                    openForm(id = null) {
                        let curso = this.cursos.find(c => c.id == id) ?? {};
                        @this.curso_id = curso.id ?? null;
                        @this.titulo = curso.titulo ?? null;
                        @this.descripcion = curso.descripcion ?? null;
                        @this.fecha_inicio = curso.fecha_inicio ?? null;
                        @this.fecha_fin = curso.fecha_fin ?? null;
                        @this.nota_minima = curso.nota_minima ?? null;
                        @this.tiempo_minutos = curso.tiempo_minutos ?? null;
                        @this.max_intentos = curso.max_intentos ?? null;
                        @this.activo = curso.activo ?? null;

                        $('#form_cursos').modal('show');

                        setTimeout(() => {
                            $('#activo').val(@this.activo).trigger('change');
                        }, 300);
                    },

                    confirmDelete(id) {
                        alertClickCallback(
                            'Desactivar curso',
                            'El curso quedará inactivo, no se eliminará del historial',
                            'warning',
                            'Confirmar',
                            'Cancelar',
                            async () => {
                                const res = await @this.desactivar(id);
                                if (res) {
                                    this.addCurso(res, true);
                                    toastRight('error', 'Curso inactivado');
                                }
                            }
                        );
                    },

                    goMateriales(id) {
                        location.href = `/admin/cursos/${id}/materiales`
                    },
                    goPreguntas(id) {
                        location.href = `/admin/cursos/${id}/preguntas`
                    },
                    goAsignaciones(id) {
                        location.href = `/admin/cursos/${id}/asignaciones`
                    },
                    goResultados(id) {
                        location.href = `/admin/cursos/${id}/resultados`
                    },
                    goResultadosFiltro(id, filtro) {
                        location.href = `/admin/cursos/${id}/resultados?filtro=${filtro}`
                    }
                }));
            </script>
        @endscript
        <script>
            function toggleDetalleCurso(id) {
                const el = document.getElementById('detalle_' + id);
                if (!el) return;
                const abierto = el.style.display === 'block';
                // cerrar todos
                document.querySelectorAll('[id^="detalle_"]').forEach(d => d.style.display = 'none');
                // abrir solo el clickeado
                if (!abierto) {
                    el.style.display = 'block';
                }
            }
        </script>
    </div>
</div>

<div>
    <div x-data="preguntas">

        <div class="content-wrapper p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>❓ Preguntas — {{ $curso->titulo }}</h4>
                <div class="d-flex gap-2">
                    <button class="btn btn-dark mr-1" @click="openForm()">Nueva pregunta</button>
                    <a href="{{ route('cursos') }}" class="btn btn-secondary">
                        ← Volver
                    </a>
                </div>
            </div>

            <div class="card">
                <div x-show="!loading">
                    <x-table id="table_preguntas">
                        <tr>
                            <th>Pregunta</th>
                            <th>Tipo</th>
                            <th>Respuestas</th>
                            <th>Acc</th>
                        </tr>
                    </x-table>
                </div>

                <div x-show="loading">
                    <x-spinner />
                </div>
            </div>

        </div>

        @include('livewire.admin.form-preguntas')

        @script
            <script>
                Alpine.data('preguntas', () => ({

                    preguntas: [],
                    loading: true,

                    init() {
                        this.getPreguntas();
                    },

                    async getPreguntas() {
                        this.loading = true;

                        this.preguntas = await @this.getPreguntas();

                        $('#body_table_preguntas').html(''); // limpiar tabla

                        for (const p of this.preguntas) {
                            this.addRow(p);
                        }

                        setTimeout(() => {
                            __resetTable('#table_preguntas');
                            this.loading = false;
                        }, 300);
                    },

                    addRow(p) {

                        let r = p.respuestas.map(x =>
                            (x.es_correcta ? '✔ ' : '') + x.respuesta
                        ).join('<br>');

                        let tr = `
                                    <tr id="preg_${p.id}">
                                        <td>${p.pregunta}</td>
                                        <td>${p.tipo == 'opcion_multiple' ? 'Opción multiple' : ' Falso/Verdadero'}</td>
                                        <td>${r}</td>
                                        <td>
                                            <div class="d-flex">
                                                <x-buttonsm click="openForm('${p.id}')">
                                                    <i class="la la-edit"></i>
                                                </x-buttonsm>
                                                <x-buttonsm click="confirmDelete('${p.id}')" color="danger">
                                                    <i class="la la-trash"></i>
                                                </x-buttonsm>
                                            </div>
                                        </td>
                                    </tr>
                                `;

                        let row = document.getElementById(`preg_${p.id}`);

                        if (row) {
                            row.outerHTML = tr; // update
                        } else {
                            $('#body_table_preguntas').prepend(tr); // create
                        }
                    },

                    async saveFront() {

                        const update = !!@this.pregunta_id;
                        const p = await @this.save();

                        if (p) {

                            // actualizar array local
                            if (update) {
                                let i = this.preguntas.findIndex(x => x.id === p.id);
                                if (i !== -1) this.preguntas[i] = p;
                            } else {
                                this.preguntas.push(p);
                            }

                            this.addRow(p);
                            $('#form_preguntas').modal('hide');

                            toastRight('success', 'Pregunta guardada');
                        }
                    },

                    openForm(id = null) {

                        let p = this.preguntas.find(x => x.id == id) ?? {};

                        @this.pregunta_id = p.id ?? null;
                        @this.pregunta = p.pregunta ?? '';
                        @this.tipo = p.tipo ?? 'opcion_multiple';

                        @this.respuestas = (p.respuestas ?? []).map(r => ({
                            texto: r.respuesta,
                            correcta: r.es_correcta
                        }));

                        $('#form_preguntas').modal('show');
                    },

                    confirmDelete(id) {

                        alertClickCallback(
                            'Eliminar pregunta',
                            'Se eliminarán también sus respuestas',
                            'warning',
                            'Eliminar',
                            'Cancelar',
                            async () => {

                                const ok = await @this.eliminar(id);

                                if (ok) {
                                    this.preguntas = this.preguntas.filter(x => x.id != id);
                                    $(`#preg_${id}`).remove();
                                    toastRight('error', 'Pregunta eliminada');
                                }
                            }
                        )
                    }

                }))
            </script>
        @endscript
    </div>
</div>

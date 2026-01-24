<div>
    <div x-data="materiales">

        <div class="content-wrapper p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">📚 Materiales — {{ $curso->titulo }}</h4>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-dark mr-1" @click="openForm()">
                        Nuevo material
                    </button>
                    <a href="{{ route('cursos') }}" class="btn btn-secondary">
                        ← Volver
                    </a>
                </div>
            </div>

            <div class="card">
                <div x-show="!loading">
                    <x-table id="table_materiales">
                        <tr>
                            <th>Orden</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Acc</th>
                        </tr>
                    </x-table>
                </div>

                <div x-show="loading">
                    <x-spinner />
                </div>
            </div>
        </div>

        {{-- MODALES --}}
        @include('livewire.admin.form-materiales')
        @include('livewire.admin.modal-preview-material')

        @script
            <script>
                Alpine.data('materiales', () => ({

                    materiales: [],
                    loading: true,

                    init() {
                        this.getMateriales();

                        $('#tipo').change(() => {
                            @this.tipo = $('#tipo').val()
                        })
                    },

                    async getMateriales() {
                        this.loading = true;

                        this.materiales = await @this.getMateriales();

                        for (const m of this.materiales) {
                            this.addRow(m);
                        }

                        setTimeout(() => {
                            __resetTable('#table_materiales');
                            this.loading = false;
                        }, 300);
                    },

                    addRow(m, is_update = false) {

                        let tr = ``;
                        if (!is_update) tr += `<tr id="mat_${m.id}">`;

                        tr += `
                        <td>${m.orden}</td>
                        <td>${m.titulo}</td>
                        <td>${m.tipo}</td>
                        <td>
                            <div class="d-flex">
                                <x-buttonsm click="openPreview('${m.id}')" color="info">
                                    <i class="la la-eye"></i>
                                </x-buttonsm>

                                <x-buttonsm click="openForm('${m.id}')">
                                    <i class="la la-edit"></i>
                                </x-buttonsm>

                                <x-buttonsm click="event.preventDefault(); confirmDelete('${m.id}')" color="danger">
                                    <i class="la la-trash"></i>
                                </x-buttonsm>
                            </div>
                        </td>
                    `;

                        if (!is_update) {
                            tr += `</tr>`;
                            $('#body_table_materiales').prepend(tr);
                        } else {
                            $(`#mat_${m.id}`).html(tr);
                        }
                    },

                    async saveFront() {

                        const is_update = @this.material_id ? true : false;
                        const material = await @this.save();

                        if (material) {

                            // 🔥 actualizar array local
                            if (is_update) {
                                const i = this.materiales.findIndex(x => x.id == material.id);
                                if (i !== -1) this.materiales[i] = material;
                            } else {
                                this.materiales.unshift(material);
                            }

                            this.addRow(material, is_update);
                            $('#form_materiales').modal('hide');

                            toastRight('success', is_update ? 'Material actualizado' : 'Material creado');
                        }
                    },

                    openForm(id = null) {

                        // ✅ NUEVO
                        if (!id) {
                            @this.limpiar();
                            setTimeout(() => $('#form_materiales').modal('show'), 120);
                            return;
                        }

                        // ✅ EDITAR
                        let m = this.materiales.find(x => x.id == id) ?? {};

                        @this.material_id = m.id ?? null;
                        @this.tipo = m.tipo ?? null;
                        @this.titulo = m.titulo ?? null;
                        @this.url = m.url ?? null;
                        @this.orden = m.orden ?? 1;
                        @this.archivo_actual = m.archivo_path ?? null;

                        setTimeout(() => {
                            $('#form_materiales').modal('show');
                        }, 150);
                    },

                    confirmDelete(id) {

                        alertClickCallback(
                            'Eliminar material',
                            'Este material se eliminará definitivamente',
                            'warning',
                            'Eliminar',
                            'Cancelar',
                            async () => {

                                const res = await @this.eliminar(id);

                                if (res) {
                                    this.materiales = this.materiales.filter(x => x.id != id);
                                    $(`#mat_${id}`).remove();
                                    toastRight('error', 'Material eliminado');
                                }
                            }
                        );
                    },

                    openPreview(id) {
                        @this.preview(id);
                        $('#modal_preview_material').modal('show');
                    },

                }))
            </script>
        @endscript

    </div>
</div>

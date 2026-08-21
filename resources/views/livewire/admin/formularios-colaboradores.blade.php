<div class="content-wrapper p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="content-header-title">Formularios de conocimiento de colaboradores</h3>
        <a href="{{ route('formulario.conocimiento') }}" class="btn btn-outline-primary">Mi formulario</a>
    </div>

    <div class="card">
        <div class="card-body" x-data="formulariosColaboradores" x-init="cargar()">
            <div x-show="loading"><x-spinner /></div>
            <div x-show="!loading" class="table-responsive">
                <table class="table table-striped" id="tabla_formularios_colaboradores">
                    <thead><tr><th>Colaborador</th><th>Documento</th><th>Estado</th><th>Enviado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <template x-for="formulario in formularios" :key="formulario.id">
                            <tr>
                                <td x-text="formulario.nombre"></td>
                                <td x-text="formulario.documento"></td>
                                <td><span class="badge" :class="badge(formulario.estado)" x-text="textoEstado(formulario.estado)"></span></td>
                                <td x-text="formulario.enviado"></td>
                                <td>
                                    @can('ver formularios colaboradores')
                                        <a :href="`{{ url('/admin/formularios-colaboradores') }}/${formulario.id}`" class="btn btn-sm btn-outline-primary mr-1">Ver</a>
                                    @endcan
                                    @can('aprobar formularios colaboradores')
                                        <button x-show="formulario.estado === 'enviado'" class="btn btn-sm btn-success mr-1" x-on:click="actualizar(formulario.id, 'aprobar')">Aprobar</button>
                                        <button x-show="formulario.estado === 'enviado'" class="btn btn-sm btn-danger" x-on:click="actualizar(formulario.id, 'rechazar')">Rechazar</button>
                                    @endcan
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @script
        <script>
            Alpine.data('formulariosColaboradores', () => ({
                formularios: [],
                loading: true,
                async cargar() {
                    this.formularios = await @this.getFormularios();
                    this.loading = false;
                },
                async actualizar(id, accion) {
                    const resultado = await Swal.fire({
                        icon: accion === 'aprobar' ? 'question' : 'warning',
                        title: accion === 'aprobar' ? '¿Aprobar formulario?' : 'Motivo del rechazo',
                        input: accion === 'rechazar' ? 'textarea' : undefined,
                        inputPlaceholder: accion === 'rechazar' ? 'Indique qué debe corregir el colaborador' : undefined,
                        showCancelButton: true,
                        confirmButtonText: accion === 'aprobar' ? 'Aprobar' : 'Rechazar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!resultado || resultado.dismiss) return;

                    if (accion === 'aprobar') {
                        Livewire.find(@this.__instance.id).aprobar(id);
                    } else {
                        Livewire.dispatch('rechazar-formulario-desde-listado', { id: id, motivo: resultado.value || '' });
                    }

                    await this.cargar();
                },
                badge(estado) {
                    return { enviado: 'badge-info', aprobado: 'badge-success', rechazado: 'badge-danger', borrador: 'badge-secondary' }[estado] || 'badge-secondary';
                },
                textoEstado(estado) {
                    return { enviado: 'Pendiente', aprobado: 'Aprobado', rechazado: 'Rechazado', borrador: 'Borrador' }[estado] || estado;
                }
            }));
        </script>
    @endscript
</div>

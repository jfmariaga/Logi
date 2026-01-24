<div x-data="data_informacion">
     <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block br_none">Información de interés</h3>
                </div>
                <div class="content-header-right col-md-6 col-12">

                    @livewire('notificaciones.form-notificacion')

                </div>
            </div>

            <div class="content-body">
                <div class="card">
                    <template x-for="nota in $wire.notas" :key="nota.id">
                        <div :class="`bs-callout-${nota.tipo}`" class="callout-border-left mt-1 p-1 lh-1">
                            <strong x-text="nota.titulo"></strong>
                            {{-- <div class="italic_sub">Creada por: </div> --}}
                            <div class="italic_sub">Expira el: <span x-text="__formatDate( nota.fecha_expired )"></span> </div>
                            <div class="italic_sub">Destinatarios: <span x-text="nota.rol ? nota.rol.name : 'Todos'"></span> </div>

                            <div class="d-flex lh-1">
                                <p x-text="nota.descripcion" class="lh-1 mt-1"></p>
    
                                <div style="margin-left:auto;min-width:80px;">
                                    <a href="javascript:" x-on:click="editNota(nota)" class="border_none btn btn-sm grey btn-outline-secondary" style="padding: 3px;">
                                        <i class="la la-edit"></i>
                                    </a>
                                    <a href="javascript:" x-on:click="confirmDeleteNota(nota.id)" class="border_none btn btn-sm grey btn-outline-secondary" style="padding: 3px;">
                                        <i class="la la-trash c_red"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

     @script
        <script>
            Alpine.data('data_informacion', () => ({
                editNota( item_old ){
                    Livewire.dispatch('edit_form_nota', item_old)
                },
                confirmDeleteNota(item_id) {
                    alertClickCallback('Eliminar',
                        'La nota será eliminada por completo, acción irreversible, desea continuar?',
                        'warning', 'Confirmar', 'Cancelar', async () => {
                            const res = await @this.eliminar(item_id);
                            if (res) {
                                toastRight('error', 'Acción realizada con éxito!');
                            }
                        });
                },

            }));
        </script>
    @endscript
</div>

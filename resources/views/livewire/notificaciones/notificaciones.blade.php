<div x-data="data_notificaciones">
    <div class="content-header">
        <h5 class="content-header-title mb-0 d-inline-block br_none">Información de interes</h5>
    </div>

    <template x-for="nota in $wire.notas" :key="nota.id">
        <div :class="`bs-callout-${nota.tipo}`" class="callout-border-left mt-1 p-1 lh-1">
            <strong x-text="nota.titulo"></strong>
            <div class="d-flex lh-1">
                <p x-text="nota.descripcion" class="lh-1 mt-1"></p>
            </div>
        </div>
    </template>

    @script
        <script>
            Alpine.data('data_notificaciones', () => ({
                init(){
                    // revisamos si hay notificaciones
                    if( @this.notas.length <= 0 ){
                        $('#content_notificaciones').addClass( 'd-none' )
                        $('#content_calendario').removeClass( 'col-md-9' )
                        $('#content_calendario').addClass( 'col-md-12' )
                    }
                }

            }));
        </script>
    @endscript
</div>


<div x-data="data_form_nota">

    <div class="btn-group float-md-right">
        <a href="javascript:" x-on:click="openForm()" id="btn_form_sede" class="btn btn-dark"> 
            Nueva nota
        </a>
    </div>  

    <x-modal id="form_nota">
        <x-slot name="title">
            <span x-show="!$wire.form_old">Agregar Nota</span>
            <span x-show="$wire.form_old">Editar Nota</span>
        </x-slot>
    
        <div class="row">
            <div class="col-md-6 mt-1">
                <x-select model="$wire.form.tipo" label="Tipo" id="tipo" required="true">
                    <option value="primary">Default</option>
                    <option value="info">Info</option>
                    <option value="warning">Warning</option>
                    <option value="success">Success</option>
                    <option value="danger">Danger</option>
                </x-select>
            </div>
            <div class="col-md-6 mt-1">
                <x-input model="$wire.form.fecha_expired" type="date" label="Fecha expiración" required="true"></x-input>
            </div>
            <div class="col-md-12 mt-1">
                <x-input model="$wire.form.titulo" label="Titulo" required="true"></x-input>
            </div>
            <div class="col-md-12 mt-1">
                <x-textarea model="$wire.form.descripcion" label="Descripción" required="true"></x-textarea>
            </div>
    
        </div>
    
        <x-slot name="footer">
            <div x-show="!loading_form">
                <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-outline-primary" x-on:click="saveFront()">Guardar</button>
            </div>
    
            <div x-show="loading_form">
                <x-spinner></x-spinner>
            </div>
        </x-slot>
    </x-modal>

    @script
        <script>
            Alpine.data('data_form_nota', () => ({
                loading_form: false,

                init(){
                    $('#tipo').change(() => {
                        @this.form.tipo = $('#tipo').val()
                    })
                    Livewire.on('edit_form_nota', ( form_old ) => {
                        this.openForm( form_old )
                    })
                },

                async saveFront() {

                    this.loading_form = true

                    const new_item = await @this.save();
                    if (new_item) {
                        $('#form_nota').modal('hide');
                        toastRight('success', 'Acción realizada con éxito!');
                    }

                    this.loading_form = false

                },

                openForm( item_old = null ) {

                    // el item original conserva las propiedades reactivas
                    if( item_old ){
                        item_old = __duplicar( item_old )
                    }

                    this.loading_form = false

                    @this.form_old          = item_old;
                    @this.form.tipo         = item_old ? item_old.tipo : '';
                    @this.form.titulo       = item_old ? item_old.titulo : '';
                    @this.form.descripcion      = item_old ? item_old.descripcion : '';
                    @this.form.fecha_expired    = item_old ? item_old.fecha_expired : '';

                    setTimeout(() => {
                        $('#tipo').val( @this.form.tipo ).trigger('change');
                    }, 300);

                    $('#form_nota').modal('show');

                },

            }));
        </script>
    @endscript
</div>


<div x-data="data_form_programacion">

    <div class="btn-group float-md-right">
        <a href="javascript:" x-on:click="openForm()" id="btn_form_sede" class="btn btn-dark"> 
            Nueva programación
        </a>
    </div>  

    <x-modal id="form_programacion">
        <x-slot name="title">
            <span x-show="!$wire.form_old">Agregar Programación</span>
            <span x-show="$wire.form_old">Editar Programación</span>
        </x-slot>
    
        <div class="row">
            <div class="col-md-12 mt-1">
                <x-select model="$wire.form.sede_id" label="Sede de trabajo" id="sede_id" required="true">
                    <option value="" sleetced disabled>Seleccionar...</option>
                    @foreach ($sedes as $item)
                        <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="col-md-6 mt-1">
                <x-input model="$wire.form.desde" type="date" label="Desde" required="true"></x-input>
            </div>
            <div class="col-md-6 mt-1">
                <x-input model="$wire.form.hasta" type="date" label="Hasta" ></x-input>
            </div>
            <div class="col-md-6 mt-1">
                <x-input model="$wire.form.hora_entrada" type="time" label="Hora de entrada"></x-input>
            </div>
            <div class="col-md-6 mt-1">
                <x-input model="$wire.form.hora_salida" type="time" label="Hora de salida"></x-input>
            </div>
            <div class="col-md-12 mt-1">
                <x-select model="$wire.form.personal" label="Personal" id="personal" multiple="true">
                    <option value="0" disabled>Seleccionar...</option>
                    @foreach ($usuarios as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} {{ $item->last_name }}</option>
                    @endforeach
                </x-select>
            </div>
    
        </div>
    
        <x-slot name="footer">
            <div class="mr-auto c_orange" x-show="$wire.form_old && $wire.form.hasta && $wire.form.desde != $wire.form.hasta">
                Esta programación aplica para varios días
            </div>
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
            Alpine.data('data_form_programacion', () => ({
                loading_form: false,

                init(){
                    $('#sede_id').change(() => {
                        @this.form.sede_id = $('#sede_id').val()
                    })
                    $('#personal').change(() => {
                        @this.form.personal = $('#personal').val()
                    })

                    Livewire.on('edit_form_programacion', ( form_old ) => {
                        this.openForm( form_old )
                    })
                },

                async saveFront() {

                    this.loading_form = true

                    const new_item = await @this.save();
                    if (new_item) {
                        $('#form_programacion').modal('hide');
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
                    @this.form.desde        = item_old ? item_old.desde : '';
                    @this.form.hasta        = item_old ? item_old.hasta : '';
                    @this.form.hora_entrada = item_old ? item_old.hora_entrada : null;
                    @this.form.hora_salida  = item_old ? item_old.hora_salida : null;
                    @this.form.sede_id      = item_old ? item_old.sede_id : null;
                    @this.form.personal     = item_old ? item_old.personal : [];

                    setTimeout(() => {
                        const personal_select = []
                        @this.form.personal.map( (u)=>{
                            personal_select.push(u.id)
                        })
                        $('#sede_id').val(@this.form.sede_id).trigger('change');
                        $('#personal').val( personal_select ).trigger('change');
                    }, 300);

                    $('#form_programacion').modal('show');

                },

            }));
        </script>
    @endscript
</div>

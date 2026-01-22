<div x-data="data_sedes">
     <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block br_none">Sedes de trabajo</h3>
                </div>
                <div class="content-header-right col-md-6 col-12">
                    <div class="btn-group float-md-right">
                        <a href="javascript:" x-on:click="openForm()" id="btn_form_sede" class="btn btn-dark"> 
                            Nuevo
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <div class="card">
                    <div x-show="!loading">
                        <x-table id="table_sedes" extra="d-none">
                            <tr>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Contacto</th>
                                <th>Estado</th>
                                <th class="d-none">Latitud</th>
                                <th class="d-none">Longitud</th>
                                <th class="d-none">Radio metros</th>
                                <th>Acc</th>
                            </tr>
                        </x-table>
                    </div>
                    <div x-show="loading">
                        <x-spinner></x-spinner>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.sedes.form-sede')


     @script
        <script>
            Alpine.data('data_sedes', () => ({
                data: [],
                loading: true,
                loading_form: false,

                init(){

                    this.getItems();

                    $('#activo').change(() => {
                        @this.form.activo = $('#activo').val()
                    })
                },

                async getItems() {
                    this.loading = true;
                    this.data = await @this.getData();

                    for (const item of this.data) {
                        await this.addItem(item);
                    }

                    setTimeout(() => {
                        __resetTable('#table_sedes', 'asc');
                        this.loading = false;
                    }, 500);
                },

                async addItem(item, is_update = false) {
                    
                    let tr = ``;

                    if (!is_update) {
                        tr += `<tr id="sede_${item.id}">`;
                    }

                    tr += `
                            <td>${print( item.nombre )}</td>
                            <td>${print( item.direccion )}</td>
                            <td>${print( item.contacto )} - ${print( item.telefono_contacto )} </td>
                            <td>${item.activo == 1 ?  '<span class="badge badge-success text-white" >Activo</span>' : '<span class="badge badge-danger text-white" >Inactivo</span>'}</td>
                            <td class="d-none">${print( item.latitud )}</td>
                            <td class="d-none">${print( item.longitud )}</td>
                            <td class="d-none">${print( item.radio_metros )}</td>
                            <td>
                                <div class="d-flex">
                                    <x-buttonsm click="openForm('${item.id}')"><i class="la la-edit"></i></x-buttonsm>
                                </div>
                            </td>`;
      
                    if (!is_update) {
                        tr += `</tr>`;
                        $('#body_table_sedes').prepend(tr);
                    } else {
                        $(`#sede_${item.id}`).html(tr);
                    }
                },
              
                async saveFront() {

                    this.loading_form = true

                    const is_update = typeof @this.form_old.id !== 'undefined' ? true : false;
                    const new_item = await @this.save();
                    if (new_item) {
                        if( is_update ){
                            for (const key in this.data) {
                                if (this.data[key].id == new_item.id) {
                                    this.data[key] = new_item;
                                }
                            }
                        }else{
                            this.data.push(new_item);

                        }
                        this.addItem(new_item, is_update)
                        $('#form_sede').modal('hide');
                        toastRight('success', 'Acción realizada con éxito!');
                    }

                    this.loading_form = false

                },

                openForm( item_id = null ) {

                    this.loading_form = false

                    let item_old = this.data.find((i) => i.id == item_id);
                    item_old = item_old ?? {};

                    @this.form_old          = item_old;
                    @this.form.nombre       = item_old ? item_old.nombre : '';
                    @this.form.direccion    = item_old ? item_old.direccion : '';
                    @this.form.contacto     = item_old ? item_old.contacto : '';
                    @this.form.telefono_contacto = item_old ? item_old.telefono_contacto : '';
                    @this.form.activo       = item_old ? item_old.activo : 1;
                    @this.form.latitud      = item_old ? item_old.latitud : '';
                    @this.form.longitud     = item_old ? item_old.longitud : '';
                    @this.form.radio_metros = item_old ? item_old.radio_metros : 150;

                    setTimeout(() => {
                        const activo = $('#activo');
                        if (activo == 0 || activo == 1) {
                            $(activo).val(@this.form.activo).trigger('change');
                        }
                    }, 300);
                    $('#form_sede').modal('show');

                },


            }));
        </script>
    @endscript
</div>

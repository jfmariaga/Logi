<div x-data="data_repositorio">

    <style>
        .head_tabla, .footer_tabla{
            display: none !important;
        }
    </style>
        
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <div class="row">
                    {{-- <div class="col-md-3">
                        <div class="card p-2" style="min-height: 75vh;">
                            <div class="content-header">
                                <h4 class="content-header-title mb-0 d-inline-block br_none">Repositorio</h4>
                            </div>
                            <div class="dropdown-divider"></div>
                            
                            @livewire('repositorio.menu')

                        </div>
                    </div>  --}}
                    <div class="col-md-12">
                         <div class="card p-2" style="min-height: 75vh;">
                            <div class="content-header">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h5 class="content-header-title mb-0 d-inline-block br_none">
                                            <a x-on:click="Livewire.dispatch('changeCarpeta', { id: 0, home: 1 })">
                                                <i class="la la-home"></i>
                                                <span>Home</span>
                                            </a>
                                            <template x-for="miga in $wire.miga_de_pan">
                                                <a x-on:click="Livewire.dispatch('changeCarpeta', { id: miga.id })">
                                                    /
                                                    <span x-text="miga.nombre"></span>
                                                </a>
                                            </template>
                                        </h5>
                                    </div>
                                    <div class="col-md-3">
                                        {{-- formulario nueva carpeta --}}
                                        @livewire('repositorio.form-carpeta')
                                       
                                        {{-- formulario file --}}
                                        @livewire('repositorio.form-file')
                                    </div>
                                </div>
                            </div>

                            {{-- content de archivos --}}
                            <div class="">
                                <div x-show="!$wire.loading">
                                    <div wire:ignore class="card-content collapse show">
                                        <div class="card-body card-dashboard">
                                            <table class="table table-striped w-100">
                                                <thead>
                                                    <tr>
                                                        <th class="w_70">Nombre</th>
                                                        <th>Fecha de modificación</th>
                                                        <th>Clase</th>
                                                        <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template x-for="carpeta in $wire.sub_carpetas" :key="carpeta.id">
                                                        <tr :class=`carpeta_repositorio_${carpeta.id}` class="pointer tr_item_repositorio " x-on:dblclick="Livewire.dispatch('changeCarpeta', { id: carpeta.id })">
                                                            <td>
                                                                <i class="la la-folder-open"></i>
                                                                <span x-text="carpeta.nombre"></span>
                                                            </td>
                                                            <td>
                                                                <span x-text="carpeta.updated_at"></span>
                                                            </td>
                                                            <td>Carpeta</td>
                                                            <td>
                                                                <div class="d-flex">
                                                                    <a href="javascript:" x-on:click="openEditCarpeta( carpeta )" class=" border_none btn btn-sm grey btn-outline-secondary " style="padding: 3px;"> 
                                                                        <i class="la la-edit"></i>
                                                                    <a>                                                                                                      
                                                                    <a href="javascript:" x-on:click="confirmDeleteCarpeta( carpeta.id )" class=" border_none btn btn-sm grey btn-outline-danger " style="padding: 3px;"> 
                                                                        <i class="la la-trash"></i>
                                                                    </a>                                                                    
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="$wire.loading">
                                    <x-spinner></x-spinner>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            // Store global
            Alpine.store('repositorio', {
                prueba: 1,
                carpetas: []
            })
        })
    </script>
    @script
        <script>
            Alpine.data('data_repositorio', () => ({
                init() {
                    __resetTableNoPaginate('#table_repositorio');
                },
                // paso toda la carpeta, para no tener que volver a consultar
                openEditCarpeta( carpeta ) {
                    Livewire.dispatch('editCarpeta', { carpeta })
                    $('#form_carpeta').modal('show');
                },
                confirmDeleteCarpeta(id) {
                    alertClickCallback('Eliminar',
                        'La carpeta se moverá a la papelera, estará ahí hasta 30 días, pasado este tiempo se eliminará por completo, desea continuar?',
                        'warning', 'Confirmar', 'Cancelar', async () => {
                            const res = await @this.eliminarCarpeta(id);
                            if (res) {
                                $(`.carpeta_repositorio_${id}`).addClass('d-none')
                                toastRight('error', 'Acción realizada con éxito');
                            }
                        });
                },
            }));
        </script>
    @endscript

</div>

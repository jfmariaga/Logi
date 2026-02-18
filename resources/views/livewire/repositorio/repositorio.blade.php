<div x-data="data_repositorio">

    <style>
        .head_tabla, .footer_tabla{
            display: none !important;
        }
    </style>
        
    <div class="app-content content">
        <div class="content-wrapper">

            {{-- gestor de archivos y carpetas --}}
            <div class="content-body" x-show="!url_file_preview">
                <div class="row">
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

                                    {{-- hay elementos para mostra --}}
                                    <div class="card-content collapse show" x-show="$wire.sub_carpetas.length > 0 || $wire.files.length > 0">
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

                                                    {{-- carpetas --}}
                                                    <template x-for="carpeta in $wire.sub_carpetas" :key="carpeta.id">
                                                        <template x-if="$wire.carpetas_user.includes(carpeta.id)">
                                                            <tr :class=`carpeta_repositorio_${carpeta.id}` class="pointer tr_item_repositorio " x-on:dblclick="Livewire.dispatch('changeCarpeta', { id: carpeta.id })" style="background-color: #f2f2f2; border-top: solid 1px #fff;">
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
                                                    </template>

                                                    {{-- archivos --}}
                                                    <template x-for="file in $wire.files" :key="file.id">
                                                        <template x-if="$wire.files_user.includes(file.id)">
                                                            <tr :class=`file_repositorio_${file.id}` class="pointer tr_item_repositorio " style="border-top: solid 1px #f2f2f2;">
                                                                <td>
                                                                    <i :class="getIcon(file.mime_type).faIcon + ' ' + getIcon(file.mime_type).color" class="la"></i>
                                                                    <span x-text="file.name"></span>
                                                                    (<span x-text="file.extension"></span>)
                                                                </td>
                                                                <td>
                                                                    <span x-text="file.updated_at"></span>
                                                                </td>
                                                                <td>Archivo</td>
                                                                <td>
                                                                    <div class="d-flex">

                                                                        {{-- fancybox solo aplica para PDF y IMG --}}
                                                                        <a x-show="getIcon( file.mime_type ).icon == 'image' || getIcon( file.mime_type ).icon == 'pdf'"
                                                                            :href="`/storage/gestion-documental/${file.original_name}`" data-fancybox
                                                                            class=" border_none btn btn-sm grey btn-outline-secondary "
                                                                            style="padding: 3px;">
                                                                            <i class="la la-eye"></i>
                                                                        </a>

                                                                        {{-- solo aplica para Excel, Word y PPTX --}}
                                                                        <a x-show="getIcon( file.mime_type ).icon == 'spreadsheet' || getIcon( file.mime_type ).icon == 'word'|| file.extension == 'pptx'" x-on:click="showDocument( file.id, file.name )" class=" border_none btn btn-sm grey btn-outline-secondary " style="padding: 3px;"> 
                                                                            <i class="la la-eye"></i>
                                                                        </a>    

                                                                        <a href="javascript:" x-on:click="openEditFile( file )" class=" border_none btn btn-sm grey btn-outline-secondary " style="padding: 3px;"> 
                                                                            <i class="la la-edit"></i>
                                                                        <a>                                                                                                      
                                                                        <a href="javascript:" x-on:click="confirmDeleteFile( file.id )" class=" border_none btn btn-sm grey btn-outline-danger " style="padding: 3px;"> 
                                                                            <i class="la la-trash"></i>
                                                                        </a>                                                                    
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- no hya elementos para mostrar --}}
                                    <div class="bs-callout-warning callout-border-left mt-1 p-1" x-show="$wire.sub_carpetas.length == 0 && $wire.files.length == 0">
                                        <strong>No se ha cargado nada en esta carpeta.</strong>
                                        <p>Puedes crear carpetas o archivos en esta sección</p>
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
   
            {{-- previsuzalizar archivos ( word, excel y ppt ) --}}
            <div class="content-body" x-show="url_file_preview">
                <div class="row">
                    <div class="col-md-12">
                         <div class="card p-2" style="min-height: 75vh;">
                            <div class="content-header">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h5 class="content-header-title mb-0 d-inline-block br_none">
                                            <a href="javascript:" x-on:click="name_file_preview  = null; url_file_preview = null;">
                                                <i class="la la-arrow-left"></i>
                                            </a>
                                            Preview ( <span x-text="name_file_preview"></span> )
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            {{-- content de archivos --}}
                            <div class="w-100" style="height:70vh;">
                                <iframe :src="url_file_preview" 
                                        width="100%" 
                                        height="100%" 
                                        frameborder="0">
                                </iframe> 
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

                name_file_preview : null,
                url_file_preview  : null,

                init() {
                    __resetTableNoPaginate('#table_repositorio');

                    Livewire.on('openOnlyOffice', ({ fileId, name }) => {
                        this.showDocument(fileId, name);
                    });
                },

                showDocument(fileId, name) {

                    this.name_file_preview = name
                    this.url_file_preview = `/onlyoffice/editor/${fileId}`
                },
                // paso toda la carpeta, para no tener que volver a consultar
                openEditCarpeta( carpeta ) {
                    Livewire.dispatch('editCarpeta', { carpeta })
                    $('#form_carpeta').modal('show');
                },

                openEditFile( file ) {
                    Livewire.dispatch('editFile', { file })
                    $('#form_file').modal('show');
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
                confirmDeleteFile(id) {
                    alertClickCallback('Eliminar',
                        'El archivo se moverá a la papelera, estará ahí hasta 30 días, pasado este tiempo se eliminará por completo, desea continuar?',
                        'warning', 'Confirmar', 'Cancelar', async () => {
                            const res = await @this.eliminarFile(id);
                            if (res) {
                                $(`.file_repositorio_${id}`).addClass('d-none')
                                toastRight('error', 'Acción realizada con éxito');
                            }
                        });
                },
                getIcon(mimeType) {
                    mimeType = mimeType.toLowerCase();

                    // Mapeo de tipos MIME a categorías
                    const mimeToIcon = {
                        'image': 'image',
                        'pdf': 'pdf',
                        'word': 'word',
                        'excel': 'spreadsheet',
                        'spreadsheet': 'spreadsheet',
                        'zip': 'archive',
                        'video': 'video',
                        'audio': 'audio'
                    };

                    // Buscar coincidencia
                    let icon = 'archive'; // valor por defecto

                    for (const [key, value] of Object.entries(mimeToIcon)) {
                        if (mimeType.includes(key)) {
                            icon = value;
                            break;
                        }
                    }

                    // Mapeo de icono a clase de fondo
                    const iconToBgClass = {
                        'image':        'text-primary',
                        'pdf':          'text-danger',
                        'word':         'text-info',
                        'document':     'text-warning',
                        'spreadsheet':  'text-success',
                        'archive':      'text-warning',
                        'video':        'text-secondary',
                        'audio':        'text-secondary'
                    };

                    // Mapeo de icono a Font Awesome
                    const iconToFaIcon = {
                        'image': 'la la-image',
                        'pdf': 'la la-file-pdf-o',
                        'word': 'la la-file-word-o',
                        'document': 'la la-file-archive-o',
                        'spreadsheet': 'la la-file-excel-o',
                        'archive': 'la la-file-archive-o',
                        'video': 'la la-file-video-o',
                        'audio': 'fas fa-file-audio',
                    };

                    return {
                        icon: icon,
                        color: iconToBgClass[icon] || 'secondary',
                        faIcon: iconToFaIcon[icon] || 'fas fa-file',
                        isImage: icon === 'image'
                    };
                },
            }));
        </script>
    @endscript

</div>

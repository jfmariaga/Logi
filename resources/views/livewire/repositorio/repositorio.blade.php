<div x-data="data_repositorio">

    <style>
        .head_tabla, .footer_tabla{
            display: none !important;
        }
    </style>
        
    <div class="app-content content">
        <div class="content-wrapper">
            {{-- gestor de archivos y carpetas --}}
            <div class="content-body">
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

                                        @can('crear gestión documental')                 
                                            {{-- formulario nueva carpeta --}}
                                            @livewire('repositorio.form-carpeta')
                                        
                                            {{-- formulario file --}}
                                            @livewire('repositorio.form-file')

                                            {{-- Crear Archivo --}}
                                            <a class="btn btn-outline-success f_right ml-1" x-on:click="$('#modal_crear_documento').modal('show')">
                                                Crear Documento
                                            </a>
                                        @endcan

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
                                                                    <div class="d-flex justify-content-end">
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
                                                                    <div class="d-flex justify-content-end">

                                                                        {{-- fancybox solo aplica para PDF y IMG --}}
                                                                        <a x-show="getIcon( file.mime_type ).icon == 'image' || getIcon( file.mime_type ).icon == 'pdf'"
                                                                            :href="`/storage/gestion-documental/${file.original_name}`" data-fancybox
                                                                            class=" border_none btn btn-sm grey btn-outline-secondary "
                                                                            style="padding: 3px;">
                                                                            <i class="la la-eye"></i>
                                                                        </a>

                                                                        @can('editar gestión documental')            
                                                                            {{-- OnlyOffice para Excel, Word y PowerPoint - Editar --}}
                                                                            <a x-show="canEditWithOnlyOffice(file.extension)" 
                                                                                x-on:click="openOnlyOffice(file.id, 'edit')" 
                                                                                class="border_none btn btn-sm grey btn-outline-secondary" 
                                                                                style="padding: 3px;"
                                                                                title="Editar documento">
                                                                                <i class="la la-pencil"></i>
                                                                            </a>    
                                                                        @endcan

                                                                        {{-- OnlyOffice para Excel, Word y PowerPoint - Ver --}}
                                                                        <a x-show="canOpenWithOnlyOffice(file.extension)" 
                                                                            x-on:click="openOnlyOffice(file.id, 'view')" 
                                                                            class="border_none btn btn-sm grey btn-outline-secondary" 
                                                                            style="padding: 3px;"
                                                                            title="Ver documento">
                                                                            <i class="la la-eye"></i>
                                                                        </a>

                                                                        <a :href="`/storage/gestion-documental/${file.original_name}`" :download="file.name" class=" border_none btn btn-sm grey btn-outline-secondary " style="padding: 3px;"> 
                                                                            <i class="la la-download"></i>
                                                                        </a>  

                                                                        @can('editar gestión documental')
                                                                            <a href="javascript:" x-show="file.carpeta_id > 0" x-on:click="openEditFile( file )" class=" border_none btn btn-sm grey btn-outline-secondary " style="padding: 3px;"> 
                                                                                <i class="la la-edit"></i>
                                                                            <a>                                                                           
                                                                        @endcan

                                                                        @can('eliminar gestión documental')           
                                                                            <a href="javascript:" x-on:click="confirmDeleteFile( file.id )" class=" border_none btn btn-sm grey btn-outline-danger " style="padding: 3px;"> 
                                                                                <i class="la la-trash"></i>
                                                                            </a>                                                                    
                                                                        @endcan
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
        </div>
    </div>

    {{-- Modal para crear documento --}}
    <div class="modal fade" id="modal_crear_documento" tabindex="-1" role="dialog" aria-labelledby="modalCrearDocumentoLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearDocumentoLabel">
                        Crear Nuevo Documento
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_crear_documento">
                        <div class="form-group">
                            <label for="doc_nombre">Nombre del documento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="doc_nombre" name="name" required>
                        </div>
                        
                        <div class="form-group mt-2">
                            <label>Tipo de documento <span class="text-danger">*</span></label>
                            <div class="row mt-1">
                                <div class="col-4">
                                    <div class="card pointer doc-type-card" data-type="xlsx" style="border: 2px solid #ddd; transition: all 0.3s;">
                                        <div class="card-body text-center p-0">
                                            <i class="la la-file-excel-o text-success" style="font-size: 48px;"></i>
                                            <h6 class="mt-2 mb-0">Excel</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card pointer doc-type-card" data-type="docx" style="border: 2px solid #ddd; transition: all 0.3s;">
                                        <div class="card-body text-center p-0">
                                            <i class="la la-file-word-o text-primary" style="font-size: 48px;"></i>
                                            <h6 class="mt-2 mb-0">Word</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card pointer doc-type-card" data-type="pptx" style="border: 2px solid #ddd; transition: all 0.3s;">
                                        <div class="card-body text-center p-0">
                                            <i class="la la-file-powerpoint-o text-warning" style="font-size: 48px;"></i>
                                            <h6 class="mt-2 mb-0">Power Point</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="doc_type" name="type" value="">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-outline-primary" id="btn_crear_documento" disabled>Crear y Abrir</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .doc-type-card:hover {
            border-color: #007bff !important;
            box-shadow: 0 2px 8px rgba(0,123,255,0.2);
        }
        .doc-type-card.selected {
            border-color: #007bff !important;
            background-color: rgba(0,123,255,0.05);
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            // Store global
            Alpine.store('repositorio', {
                prueba: 1,
                carpetas: []
            })
        })

        // Funcionalidad del modal crear documento
        document.addEventListener('DOMContentLoaded', function() {
            const docTypeCards = document.querySelectorAll('.doc-type-card');
            const docTypeInput = document.getElementById('doc_type');
            const docNombre = document.getElementById('doc_nombre');
            const btnCrear = document.getElementById('btn_crear_documento');

            // Selección de tipo de documento
            docTypeCards.forEach(card => {
                card.addEventListener('click', function() {
                    docTypeCards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    docTypeInput.value = this.dataset.type;
                    validateForm();
                });
            });

            // Validación del formulario
            docNombre.addEventListener('input', validateForm);

            function validateForm() {
                const isValid = docNombre.value.trim() !== '' && docTypeInput.value !== '';
                btnCrear.disabled = !isValid;
            }

            // Crear documento
            btnCrear.addEventListener('click', async function() {
                if (btnCrear.disabled) return;

                const originalText = btnCrear.innerHTML;
                btnCrear.innerHTML = '<i class="la la-spinner la-spin"></i> Creando...';
                btnCrear.disabled = true;

                try {
                    const response = await fetch('/onlyoffice/create-document', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            name: docNombre.value.trim(),
                            type: docTypeInput.value,
                            folder_id: @this.folder_id || null
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        toastRight('success', 'Documento creado exitosamente');
                        $('#modal_crear_documento').modal('hide');
                        
                        // Limpiar formulario
                        docNombre.value = '';
                        docTypeInput.value = '';
                        docTypeCards.forEach(c => c.classList.remove('selected'));
                        
                        // Redirigir al editor
                        window.location.href = data.redirect_url;
                    } else {
                        toastRight('error', data.message || 'Error al crear el documento');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    toastRight('error', 'Error de conexión al crear el documento');
                } finally {
                    btnCrear.innerHTML = originalText;
                    btnCrear.disabled = false;
                }
            });

            // Limpiar al cerrar modal
            $('#modal_crear_documento').on('hidden.bs.modal', function() {
                docNombre.value = '';
                docTypeInput.value = '';
                docTypeCards.forEach(c => c.classList.remove('selected'));
                btnCrear.disabled = true;
            });
        });
    </script>
    @script
        <script>
            Alpine.data('data_repositorio', () => ({

                // Extensiones soportadas por OnlyOffice
                onlyoffice_viewable: ['doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'odt', 'fodt', 'ott', 'rtf', 'txt', 'djvu', 'fb2', 'epub', 'xps', 'xls', 'xlsx', 'xlsm', 'xlt', 'xltx', 'xltm', 'ods', 'fods', 'ots', 'csv', 'pps', 'ppsx', 'ppsm', 'ppt', 'pptx', 'pptm', 'pot', 'potx', 'potm', 'odp', 'fodp', 'otp'],
                onlyoffice_editable: ['docx', 'xlsx', 'pptx', 'ppsx', 'odt', 'ods', 'odp', 'csv', 'txt'],

                init() {
                    __resetTableNoPaginate('#table_repositorio');
                },

                // Verificar si se puede abrir con OnlyOffice
                canOpenWithOnlyOffice(extension) {
                    if (!extension) return false;
                    return this.onlyoffice_viewable.includes(extension.toLowerCase());
                },

                // Verificar si se puede editar con OnlyOffice
                canEditWithOnlyOffice(extension) {
                    if (!extension) return false;
                    return this.onlyoffice_editable.includes(extension.toLowerCase());
                },

                // Abrir documento con OnlyOffice
                openOnlyOffice(fileId, mode = 'view') {
                    window.location.href = `/onlyoffice/editor/${fileId}?mode=${mode}`;
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

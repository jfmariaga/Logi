<div x-data="gestion_documental" x-on:file-uploaded.window="isDragging = false" class="position-relative min-vh-50">

    <!-- Overlay de arrastrar -->
    @can('crear gestión documental')
        <div x-show="isDragging" x-transition:enter="animate__animated animate__fadeIn"
            x-transition:leave="animate__animated animate__fadeOut" class="position-fixed fixed-top fixed-bottom"
            style="z-index: 1050; background-color: rgba(0, 123, 255, 0.2); backdrop-filter: blur(2px);">
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="bg-white rounded-lg shadow-lg p-5 border border-2 border-dashed border-primary"
                    style="border-radius:20px;">
                    <div class="text-center">
                        <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3"></i>
                        <h4 class="font-weight-bold text-dark mb-2">
                            Suelta los archivos aquí
                        </h4>
                        <p class="text-muted">
                            Arrastra y suelta para subir
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input file oculto -->
        <input type="file" x-ref="fileInput" wire:model="files" multiple accept="{{ implode(',', $allowedMimes) }}"
            class="d-none">
    @endcan


    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block br_none">
                        Gestión documental
                        <div class="italic_sub">Puede arrastar sus archivos en esta zona para cargarlos</div>
                    </h3>
                </div>
                @can('crear gestión documental')
                    <div class="content-header-right col-md-6 col-12">
                        <div class="btn-group float-md-right">
                            <a href="javascript:" x-on:click="openFileDialog" id="btn_form_personal" class="btn btn-dark">
                                Cargar Archivos
                            </a>
                        </div>
                    </div>
                @endcan
            </div>
            <div class="content-body">
                <div class="card p-2 " style="min-height: 75vh;">
                    <div class="card-body text-center">

                        <!-- Indicador de carga -->
                        <div x-show="$wire.cargando" class="row justify-content-center mb-4">
                            <div class="col-md-10 col-lg-8">
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <x-spinner />
                                        <span class="font-weight-medium">
                                            Subiendo archivos...
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Archivos subidos -->
                        <div x-show="$wire.uploadedFiles.length > 0" class="card">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="content-header-title mb-0 d-inline-block br_none f-left">
                                        <i class="fas fa-folder-open mr-2"></i>
                                        Archivos subidos ( <span x-text="$wire.uploadedFiles.length"></span> )
                                    </h5>
                                </div>
                                {{-- buscar documento --}}
                                <div class="col-md-4">
                                    <input x-model="search" type="text" class="form-control"
                                        placeholder="Buscar documento...">
                                </div>
                            </div>

                            <div class="list-group list-group-flush mt-2">
                                <template x-for="file in filterFiles" :key="file.id">
                                    <div :class="`item_file_${file.id}`" class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <!-- Icono según tipo -->
                                                <div class="mr-3">
                                                    <div :class="getIcon(file.mime_type).bgClass"
                                                        class="rounded p-1 text-white">
                                                        <i :class="getIcon(file.mime_type).faIcon"
                                                            class="fa-lg text-white" style="font-size:40px;"></i>
                                                    </div>
                                                </div>

                                                <!-- Información del archivo -->
                                                <div class="text-justify">
                                                    <div
                                                        class="font-weight-bold text-dark text-decoration-none hover-text-primary">
                                                        <span x-text="file.original_name"></span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <small class="text-muted mr-3">
                                                            <i class="fas fa-hdd mr-1"></i>
                                                            <span x-text="bytesToMB( file.size )"></span>
                                                        </small>
                                                        <small class="text-muted">
                                                            <i class="far fa-clock mr-1"></i>
                                                            <span x-text=" __formatDateTime( file.created_at )"></span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Botones de acción -->
                                            <div class="btn-group" role="group">
                                                <div class="d-flex">

                                                    {{-- fancybox solo aplica para PDF y IMG --}}
                                                    <a x-show="getIcon( file.mime_type ).icon == 'image' || getIcon( file.mime_type ).icon == 'pdf'"
                                                        :href="file.url" data-fancybox
                                                        class=" border_none btn btn-sm grey btn-outline-secondary "
                                                        style="padding: 3px;">
                                                        <i class="la la-eye"></i>
                                                    </a>

                                                    {{-- solo aplica para Excel y Word -- se pone en marcha en prod --}}
                                                    <a x-show="getIcon( file.mime_type ).icon == 'spreadsheet' || getIcon( file.mime_type ).icon == 'word'|| file.extension == 'pptx'" x-on:click="showDocument( file.url )" data-toggle="modal" data-target="#documentModal" class=" border_none btn btn-sm grey btn-outline-secondary " style="padding: 3px;"> 
                                                        <i class="la la-eye"></i>
                                                    </a>      

                                                    <a :href="file.url" :download="file.original_name"
                                                        class=" border_none btn btn-sm grey btn-outline-secondary "
                                                        style="padding: 3px;">
                                                        <i class="la la-download"></i>
                                                    </a>
                                                    @can('eliminar gestión documental')
                                                        <a href="javascript:" x-on:click="confirmDelete( file )"
                                                            class=" border_none btn btn-sm grey btn-outline-danger "
                                                            style="padding: 3px;">
                                                            <i class="la la-trash"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- no se han subido archivos --}}
                        @can('crear gestión documental')
                            <div x-show="$wire.uploadedFiles.length == 0" class="mb-4">
                                <i class="la la-cloud-upload fa-3x text-secondary mb-3" style="font-size:11rem;"></i>
                                <h3 class=" font-weight-bold text-dark">
                                    Subir archivos
                                </h3>
                                <p class="card-text text-muted mb-3">
                                    Arrastra y suelta archivos aquí
                                </p>
                                <p class="text-muted small mb-4">
                                    Tipos permitidos: imágenes, PDF, documentos, hojas de cálculo, etc.<br>
                                    Tamaño máximo: 10MB por archivo
                                </p>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- visualizar Excel y Word --}}
    <div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vista previa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe :src="`https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(documentURL)}`" 
                            width="100%" 
                            height="600px" 
                            frameborder="0">
                    </iframe> 
                </div>
            </div>
        </div>
    </div>


    <!-- Estilos CSS personalizados -->
    <style>
        .border-dashed {
            border-style: dashed !important;
        }

        .hover-border-primary:hover {
            border-color: #007bff !important;
        }

        .hover-text-primary:hover {
            color: #007bff !important;
        }

        .dragging-active {
            cursor: copy !important;
        }

        .dragging-active * {
            pointer-events: none;
        }

        .dragging-active [x-show="isDragging"] * {
            pointer-events: auto;
        }

        /* Animaciones para iconos */
        .fa-cloud-upload-alt {
            transition: transform 0.3s;
        }

        .card:hover .fa-cloud-upload-alt {
            transform: translateY(-5px);
        }

        /* Estilo para el overlay de drag & drop */
        .fixed-top.fixed-bottom {
            left: 0;
            right: 0;
        }
    </style>

    @script
        <script>
            Alpine.data('gestion_documental', () => ({
                isDragging: false,
                fileInput: null,
                search: '',
                listFilter: [],
                documentURL: '',

                init() {
                    this.fileInput = this.$refs.fileInput;
                    this.setupDragAndDrop();
                },

                // get hace que se actualice cada vez que una de sus variables cambia
                get filterFiles() {

                    if (this.search.length > 0) {
                        this.search.trim().toLowerCase()
                        data = __duplicar(@this.uploadedFiles).filter((i) => i.original_name.trim()
                            .toLowerCase().includes(this.search))
                    } else {
                        data = __duplicar(@this.uploadedFiles)
                    }

                    return data

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
                        'image': 'bg-primary',
                        'pdf': 'bg-danger',
                        'word': 'bg-info',
                        'document': 'bg-warning',
                        'spreadsheet': 'bg-success',
                        'archive': 'bg-warning',
                        'video': 'bg-secondary',
                        'audio': 'bg-secondary'
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
                        bgClass: iconToBgClass[icon] || 'bg-secondary',
                        faIcon: iconToFaIcon[icon] || 'fas fa-file',
                        isImage: icon === 'image'
                    };
                },

                showDocument(url) {
                    // organizo URL con dominimo, solo funciona con https
                    const url_completa = `https://logisticarga.com/${url}`;
                    // console.log({url_completa})
                    this.documentURL = url_completa

                    // ejemplo de un archivo ya expuesto en https
                    // this.documentURL = 'https://cd11.neum.app/publicFTP/templates_excel/cuentas_banco_template.xlsx'
                },

                bytesToMB(bytes, decimals = 2) {
                    const mb = bytes / 1048576; // 1024 * 1024
                    return mb.toFixed(decimals) + ' MB';
                },
                setupDragAndDrop() {
                    const handleDragEnter = (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.isDragging = true;
                    };

                    const handleDragLeave = (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        if (!this.$el.contains(e.relatedTarget)) {
                            this.isDragging = false;
                        }
                    };

                    const handleDragOver = (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                    };

                    const handleDrop = (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        this.isDragging = false;

                        const files = e.dataTransfer.files;
                        if (files.length > 0) {
                            this.fileInput.files = files;
                            this.fileInput.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                            @this.cargando = true;
                        }
                    };

                    window.addEventListener('dragenter', handleDragEnter);
                    window.addEventListener('dragleave', handleDragLeave);
                    window.addEventListener('dragover', handleDragOver);
                    window.addEventListener('drop', handleDrop);

                    this.$watch('isDragging', (value) => {
                        if (value) {
                            document.body.classList.add('dragging-active');
                        } else {
                            document.body.classList.remove('dragging-active');
                        }
                    });
                },
                openFileDialog() {
                    this.fileInput.click();
                },
                confirmDelete(file) {
                    alertClickCallback('Eliminar',
                        `El archivo ( ${ file.original_name } ) será eliminado por completo`,
                        'warning', 'Continuar', 'Cancelar', async () => {
                            const res = await @this.eliminarArchivo(file);
                            if (res) {
                                toastRight('error', 'Archivo eliminado');
                                @this.uploadedFiles = __duplicar(@this.uploadedFiles).filter((i) => i.id !=
                                    file.id)
                                // $(`.item_file_${file.id}`).addClass('d-none')
                            }
                        });
                },
            }));
        </script>
    @endscript

</div>

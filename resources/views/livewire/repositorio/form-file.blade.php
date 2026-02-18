<div x-data="form_file" >

<style>
.drop-zone {
    border: 3px dashed #dee2e6;
    border-radius: 10px;
    padding: 40px 20px;
    text-align: center;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.drop-zone.dragover {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
}

.drop-zone.has-file {
    border-style: solid;
    border-color: #198754;
    background-color: rgba(25, 135, 84, 0.05);
}

.file-preview {
    animation: fadeIn 0.5s ease;
}

.file-icon {
    margin-bottom: 15px;
}

.file-icon.pdf { color: #dc3545; }
.file-icon.image { color: #198754; }
.file-icon.document { color: #0d6efd; }

.file-name {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 5px;
    word-break: break-all;
}

.file-size {
    color: #6c757d;
    font-size: 14px;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

    <a class="btn btn-outline-dark f_right ml-1" x-on:click="$('#form_file').modal('show')">
        Agregar archivo
    </a>

    <x-modal id="form_file">
        <x-slot name="title">
            <span>Agregar archivo</span>
        </x-slot>

        <div class="row">
            <div class="col-md-12 mt-1">
                <x-input model="$wire.form_file.nombre" type="text" label="Nombre personalizado" placeholder="Opcional..."></x-input>
            </div>
            <div class="col-md-12 mt-1">
                <x-select model="$wire.form_file.roles" label="Compartir con los sgtes Roles" id="form_file_roles" multiple="1">
                    <option value="0" disabled selected>Agregar roles... </option>
                    @foreach ( $roles as $role )
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="col-md-12 mt-1">
                <x-select model="$wire.form_file.usuarios" label="Compartir con los sgtes Usuarios" id="form_file_usuarios" multiple="1">
                    <option value="0" disabled selected>Agregar usuarios... </option>
                    @foreach ( $users as $user )
                        <option value="{{ $user->id }}">{{ $user->name . ' ' . $user->last_name }}</option>
                    @endforeach
                </x-select>
            </div>


            <div class="col-md-12 mt-2">
                <!-- Input file oculto -->
                <input type="file" x-ref="fileInput" wire:model="form_file.file" class="d-none">

                <div class="single-file-upload" x-on:click="fileInput.click()">

                    <!-- Drop Zone -->
                    <div id="singleDropZone" 
                        :class="isDragging ? 'has-file' : 'empty'"
                        class="drop-zone"
                        wire:ignore>

                        <div x-show="$wire.loading_file" class="mb-3">
                            <x-spinner></x-spinner>
                        </div>
                        
                        <!-- Estado vacío -->
                        <div class="empty-state" x-show="!$wire.loading_file && !isDragging && !$wire.form_file.name_tmp">
                            <i class="la la-cloud-upload fa-3x text-secondary mb-1" style="font-size:5rem;"></i>
                            <h5 class="mb-2">Arrastra tu archivo aquí</h5>
                            <p class="text-muted mb-0">o haz clic para seleccionar</p>
                        </div>

                        {{-- ya hay un archivo cargado --}}
                        <div class="empty-state" x-show="!$wire.loading_file && !isDragging && $wire.form_file.name_tmp">
                             <i :class="getIcon($wire.form_file.type_tmp).faIcon + ' ' + getIcon($wire.form_file.type_tmp).color" class="la fa-3x text-secondary mb-1" style="font-size:5rem;"></i>

                            <h5 class="mb-2" x-text="$wire.form_file.name_tmp"></h5>
                            <p class="text-muted mb-0" x-show="!$wire.form_file.id">Solo puedes cargar un archivo a la vez</p>
                            <p class="text-muted mb-0 c_orange" x-show="$wire.form_file.id">No puede editar el archivo cargado, si se equivocó al momento de cargarlo, debe eliminarlo y cargar el correcto</p>
                        </div>

                        <!-- Se esta arrastrando -->
                        <div class="dragging-state" x-show="isDragging">
                            <i class="la la-cloud-upload fa-3x text-secondary mb-1" style="font-size:5rem;"></i>
                            <h5 class="mb-2">Suelta tu archivo aquí</h5>
                            <p class="text-muted mb-0">&nbsp;</p>
                        </div>

                    </div>
                    
                </div>
            </div>

        </div>

        <x-slot name="footer">
            <span  x-show="!loading">
                <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-outline-primary" x-on:click="saveFile()">Guardar</button>
            </span>
            <div x-show="loading">
                <x-spinner></x-spinner>
            </div>
        </x-slot>
    </x-modal>

    @script
        <script>
            Alpine.data('form_file', () => ({

                isDragging: false,
                fileInput: null,
                loading: false,

                init() {
                    
                    this.fileInput = this.$refs.fileInput;
                    this.setupDragAndDrop();

                    $('#form_file_roles').change(() => {
                        val = $('#form_file_roles').val()
                        @this.form_file.roles = val
                    })
                    $('#form_file_usuarios').change(() => {
                        val = $('#form_file_usuarios').val()
                        @this.form_file.usuarios = val
                    })

                    $('#form_file').on('hidden.bs.modal', function (e) {
                        @this.vaciarFormFile()
                        limiparSelect2( 'form_file_usuarios' )
                        limiparSelect2( 'form_file_roles' )
                    });

                    Livewire.on('editFile', ({ file }) =>{
                        @this.form_file.id        = file.id
                        @this.form_file.nombre    = file.name
                        @this.form_file.type_tmp  = file.mime_type
                        @this.form_file.name_tmp  = file.name
                        if( file.usuarios ){
                            const usuarios_select = []
                            file.usuarios.map( (u)=>{
                                usuarios_select.push(u.user_id)
                            })
                            setTimeout(() => {             
                                @this.form_file.usuarios = usuarios_select
                                $('#form_file_usuarios').val( usuarios_select ).select2().trigger('change');
                            }, 50);
                        }
                        if( file.roles ){
                            const roles_select = []
                            file.roles.map( (r)=>{
                                roles_select.push(r.role_id)
                            })
                            setTimeout(() => {             
                                @this.form_file.roles = roles_select
                                $('#form_file_roles').val( roles_select ).select2().trigger('change');
                            }, 50);
                        }
                    })
                },

                async saveFile() {
                    @this.loading = true; 
                    const new_file = await @this.save();
                    @this.loading = false; 
                    if (new_file) {
                        $('#form_file').modal('hide');
                        toastRight('success', 'Acción realizada con éxito');
                    }
                },

                async createBlank(type) {
                    @this.loading = true;
                    const res = await @this.createBlank(type);
                    @this.loading = false;

                    if (res && res.ok) {
                        $('#form_file').modal('hide');
                        toastRight('success', 'Documento creado con éxito');
                        Livewire.dispatch('openOnlyOffice', { fileId: res.file_id, name: res.name });
                    } else {
                        toastRight('error', (res && res.message) ? res.message : 'No se pudo crear el documento');
                    }
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
                            @this.loading_file = true;
                            this.fileInput.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                            // @this.cargando = true;
                        }
                    };

                    window.addEventListener('dragenter', handleDragEnter);
                    window.addEventListener('dragleave', handleDragLeave);
                    window.addEventListener('dragover', handleDragOver);
                    window.addEventListener('drop', handleDrop);

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
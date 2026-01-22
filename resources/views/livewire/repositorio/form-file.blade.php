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
            <div class="col-md-6 mt-1">
                <x-input model="$wire.form_carpeta.nombre" type="text" label="Nombre personalizado" placeholder="Opcional..."></x-input>
            </div>

            <div class="col-md-6 mt-1">
                <x-select model="$wire.form_carpeta.privada" label="Carpeta privada" id="privada" no_search="Infinity">
                    <option value="1">Si</option>
                    <option value="0">No</option>
                </x-select>
            </div>
            <div class="col-md-12 mt-1" x-show="$wire.form_carpeta.privada != 1">
                <x-select model="$wire.form_carpeta.usuarios" label="Compartir con los sgtes Usuarios" id="usuarios" multiple="1">
                    <option value="0" disabled selected>Agregar usuarios... </option>
                    @foreach ( $users as $user )
                        <option value="{{ $user->id }}">{{ $user->name . ' ' . $user->last_name }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="col-md-12 mt-2">
                <div class="single-file-upload">
                    <!-- Drop Zone -->
                    <div id="singleDropZone" 
                        class="drop-zone {{ $file ? 'has-file' : 'empty' }}"
                        wire:ignore>
                        
                        @if(!$file)
                            <!-- Estado vacío -->
                            <div class="empty-state">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i>
                                <h5 class="mb-2">Arrastra tu archivo aquí</h5>
                                <p class="text-muted mb-0">o haz clic para seleccionar</p>
                            </div>
                        @else
                            <!-- Estado con archivo -->
                            <div class="file-preview">
                                <div class="file-icon {{ $fileType }}">
                                    <i class="fas {{ $fileIcon }} fa-3x"></i>
                                </div>
                                <div class="file-name">{{ $fileName }}</div>
                                <div class="file-size">{{ $fileSize }}</div>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-secondary mt-3"
                                        wire:click="removeFile">
                                    <i class="fas fa-times me-1"></i>Cambiar Archivo
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Input oculto -->
                    <input type="file" 
                        id="singleFileInput" 
                        class="d-none"
                        x-model="$wire.file">
                    
                    <!-- Información adicional -->
                    @if($file)
                        <div class="file-info mt-3 p-3 bg-light rounded">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas {{ $fileIcon }} fa-2x {{ $fileType }}"></i>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">{{ $fileName }}</div>
                                    <small class="text-muted">
                                        {{ $fileSize }} • {{ $fileExtension }}
                                    </small>
                                </div>
                                <div class="col-auto">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            wire:click="removeFile">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Progreso de subida -->
                            @if($uploadProgress > 0)
                                <div class="mt-3">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                            role="progressbar" 
                                            style="width: {{ $uploadProgress }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1 text-center">
                                        {{ $uploadProgress }}% completado
                                    </small>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Mensajes de error -->
                    @error('file')
                        <div class="alert alert-danger mt-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

        </div>

        <x-slot name="footer">
            <span  x-show="!$wire.loading">
                <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-outline-primary" x-on:click="$wire.save()">Guardar</button>
            </span>
            <div x-show="$wire.loading">
                <x-spinner></x-spinner>
            </div>
        </x-slot>
    </x-modal>

    @script
        <script>
            Alpine.data('form_file', () => ({
                init() {

                },
            }));
        </script>
    @endscript

    <script>
        $(document).ready(function() {
            const dropZone = $('#singleDropZone');
            const fileInput = $('#singleFileInput');
            
            // Clic en la drop zone
            dropZone.on('click', function() {
                fileInput.click();
            });
            
            // Drag & Drop
            dropZone.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.addClass('dragover');
            });
            
            dropZone.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.removeClass('dragover');
            });
            
            dropZone.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.removeClass('dragover');
                
                const file = e.originalEvent.dataTransfer.files[0];
                if (file) {
                    // Asignar archivo al input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput[0].files = dataTransfer.files;
                    
                    // Disparar evento change para Livewire
                    fileInput.trigger('change');
                }
            });
            
            // Cuando Livewire actualice el componente
            Livewire.on('fileUploaded', () => {
                dropZone.removeClass('dragover');
            });
        });
    </script>
</div>
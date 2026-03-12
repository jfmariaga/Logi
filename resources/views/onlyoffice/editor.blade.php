<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $file->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/only-office.css?v=' . env('VERSION_STYLE')) }}">
</head>
<body>

    <div class="viewer-container">

        {{-- Barra de herramientas del editor/visor embebido --}}
        <div id="editor-toolbar" class="editor-toolbar">
            <div class="editor-toolbar-info">
                <a href="javascript:" onclick="window.history.back();" class="btn btn-outline btn-sm">← Volver</a>
                <h3>{{ $file->name }}.{{ $file->extension }}</h3>
                @if($mode === 'edit')
                    <span class="badge badge-view">✏️ Modo Edición</span>
                @else
                    <span class="badge badge-view">👁️ Solo Lectura</span>
                @endif
            </div>
            {{-- hay que revisar si puede editar --}}
            @can('editar gestión documental')
                <div class="editor-toolbar-actions">
                    @if($mode === 'view' && $canEdit)
                        <a href="{{ route('onlyoffice.editor', ['fileId' => $file->id, 'mode' => 'edit']) }}" class="btn btn-primary btn-sm">
                            Editar
                        </a>
                    @endif
                </div>
            @endcan
        </div>
        
        {{-- Contenedor del iframe embebido --}}
        <div id="embedded-editor" class="embedded-editor-container">
            <div id="ds-frame" style="width: 100%; height: 100%;"></div>
        </div>
        
        {{-- Contenedor de carga inicial --}}
        <div id="docspace-container" class="loading-container">
            <div class="docspace-instructions">
                <div class="" style="vertical-align: middle; justufy-content: center; display: flex; flex-direction: column; align-items: center;">
                    <div class="loading-spinner"></div>
                    <h2>Preparando el documento</h2>
                    <p id="docspace-status" style="margin-top: 15px; text-align: center; color: #666;">
                        Por favor espere...
                    </p>
                </div>    
            </div>
        </div>

        {{-- Error container --}}
        <div id="error-container" class="error-container">
            <h2>Error al cargar el documento</h2>
            <p id="error-message">No se pudo cargar el documento.</p>
            <a href="{{ route('gestion-documental') }}" class="btn btn-primary">Volver al repositorio</a>
        </div>
    </div>

    <script>
        const fileId = {{ $file->id }};
        const csrfToken         = document.querySelector('meta[name="csrf-token"]').content;
        const documentMode      = '{{ $mode }}'; // 'view' o 'edit'
        let docspaceWindow      = null;
        let docspaceEditUrl     = null;
        let currentMode         = null;
        let embedData           = null;

        // Subir archivo a DocSpace y abrir editor/visor
        async function uploadToDocSpace(displayMode = 'embedded') {
            currentMode = displayMode;
            const btnEmbedded = document.getElementById('btn-upload-embedded');
            const btnWindow = document.getElementById('btn-upload-window');
            const status = document.getElementById('docspace-status');
            
            if (btnEmbedded) btnEmbedded.disabled = true;
            if (btnWindow) btnWindow.disabled = true;
            status.innerHTML = '⏳ Preparando documento en DocSpace...';
            
            try {
                const response = await fetch(`/onlyoffice/upload/${fileId}?mode=${documentMode}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    docspaceEditUrl = data.access_url || data.public_url || data.edit_url;
                    embedData = data.embed;
                    
                    if (displayMode === 'embedded' && embedData && embedData.available) {
                        // Modo embebido: usar SDK de DocSpace
                        openEmbeddedEditor(embedData);
                        const modeText = documentMode === 'edit' 
                            ? '✅ Editor cargado. Los cambios se guardan automáticamente en DocSpace.'
                            : '✅ Documento cargado en modo lectura.';
                        status.innerHTML = modeText;
                    } else if (displayMode === 'embedded') {
                        // Fallback: si no hay SDK disponible, abrir en ventana
                        status.innerHTML = '⚠️ El visor embebido no está disponible. Abriendo en ventana...';
                        openInNewWindow();
                    } else {
                        // Modo ventana: abrir en nueva ventana
                        openInNewWindow();
                        status.innerHTML = '✅ Archivo abierto en nueva ventana.';
                    }
                    
                    // Mostrar acciones de sincronización solo en modo edición
                    const syncActions = document.getElementById('sync-actions');
                    if (syncActions && documentMode === 'edit') {
                        syncActions.style.display = 'flex';
                    }
                    
                    const btnText = documentMode === 'edit' ? '📄 Editar aquí' : '📄 Ver aquí';
                    if (btnEmbedded) btnEmbedded.innerHTML = btnText;
                    if (btnWindow) btnWindow.innerHTML = '🔗 Abrir en ventana';
                    if (btnEmbedded) btnEmbedded.disabled = false;
                    if (btnWindow) btnWindow.disabled = false;
                    
                } else {
                    throw new Error(data.message || 'Error desconocido');
                }
            } catch (error) {
                console.error('Error completo:', error);
                status.innerHTML = `❌ ${error.message}`;
                // Mostrar botones manuales en caso de error
                const manualActions = document.getElementById('manual-actions');
                if (manualActions) manualActions.style.display = 'flex';
                if (btnEmbedded) btnEmbedded.disabled = false;
                if (btnWindow) btnWindow.disabled = false;
            }
        }

        // Abrir editor/visor embebido usando SDK de DocSpace
        function openEmbeddedEditor(embed) {
            const container = document.getElementById('docspace-container');
            const embeddedEditor = document.getElementById('embedded-editor');
            const toolbar = document.getElementById('editor-toolbar');
            
            // Ocultar instrucciones y mostrar editor
            container.classList.add('hidden');
            embeddedEditor.classList.add('active');
            toolbar.classList.add('active');
            
            // Cargar SDK de DocSpace dinámicamente
            const script = document.createElement('script');
            script.src = embed.sdk_url;
            script.async = true;
            script.onerror = function() {
                console.error('Error cargando SDK de DocSpace');
                document.getElementById('ds-frame').innerHTML = 
                    '<div style="padding: 40px; text-align: center; color: #e74c3c;">' +
                    '<h3>Error al cargar el editor</h3>' +
                    '<p>No se pudo cargar el SDK de DocSpace.</p>' +
                    '<button class="btn btn-primary" onclick="openInNewWindow()">Abrir en ventana nueva</button>' +
                    '</div>';
            };
            document.body.appendChild(script);
            
            console.log('SDK URL:', embed.sdk_url);
        }

        // Abrir en nueva ventana
        function openInNewWindow() {
            if (docspaceEditUrl) {
                docspaceWindow = window.open(docspaceEditUrl, '_blank', 'width=1200,height=800');
            }
        }

        // Copiar URL al portapapeles
        function copyPublicUrl() {
            const input = document.getElementById('public-url-input');
            input.select();
            document.execCommand('copy');
            
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '✅ Copiado!';
            setTimeout(() => {
                btn.innerHTML = originalText;
            }, 2000);
        }

        // Manejar errores
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            console.error('Error:', msg, url, lineNo, columnNo, error);
            return false;
        };

        // Cargar automáticamente el editor embebido al abrir la página
        document.addEventListener('DOMContentLoaded', function() {
            uploadToDocSpace();
        });
    </script>
</body>
</html>

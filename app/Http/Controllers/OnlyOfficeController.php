<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Repositorio\File;
use App\Models\Repositorio\FileHistoral;

class OnlyOfficeController extends Controller
{
    /**
     * Tipos de documento soportados
     */
    protected $documentTypes = [
        // Documentos de texto
        'doc'  => 'word',
        'docx' => 'word',
        'docm' => 'word',
        'odt'  => 'word',
        'rtf'  => 'word',
        'txt'  => 'word',
        'pdf'  => 'pdf',
        
        // Hojas de cálculo
        'xls'  => 'cell',
        'xlsx' => 'cell',
        'xlsm' => 'cell',
        'ods'  => 'cell',
        'csv'  => 'cell',
        
        // Presentaciones
        'ppt'  => 'slide',
        'pptx' => 'slide',
        'pptm' => 'slide',
        'odp'  => 'slide',
    ];

    /**
     * Extensiones editables
     */
    protected $editableExtensions = [
        'docx', 'xlsx', 'pptx', 'odt', 'ods', 'odp', 'csv', 'txt'
    ];

    /**
     * Muestra el visor/editor para un archivo
     */
    public function editor(Request $request, $fileId)
    {
        $file = File::findOrFail($fileId);
        $mode = $request->get('mode', 'view');
        $extension = strtolower($file->extension);
        
        // Generar URL pública del archivo
        $fileUrl = url('/storage/gestion-documental/' . $file->original_name);
        
        // Determinar qué visor usar
        $viewerType = $this->getViewerType($extension, $mode);
        
        return view('onlyoffice.editor', [
            'file' => $file,
            'fileUrl' => $fileUrl,
            'mode' => $mode,
            'viewerType' => $viewerType,
            'docspaceUrl' => config('services.onlyoffice.docspace_url'),
            'canEdit' => in_array($extension, $this->editableExtensions),
        ]);
    }

    /**
     * Determina qué tipo de visor usar
     */
    protected function getViewerType(string $extension, string $mode): string
    {
        // Para formatos de Office, siempre usar DocSpace (tanto view como edit)
        if (in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'csv', 'txt'])) {
            return 'docspace';
        }
        
        // Para PDF, usar visor nativo del navegador
        if ($extension === 'pdf') {
            return 'native';
        }
        
        // Para otros formatos, intentar con DocSpace
        return 'docspace';
    }

    /**
     * Autenticarse en DocSpace y obtener token
     */
    protected function getDocSpaceToken(): ?string
    {
        $cacheKey = 'docspace_token_' . Auth::id();
        
        // Intentar obtener del cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $docspaceUrl = config('services.onlyoffice.docspace_url');
        $user = config('services.onlyoffice.docspace_user');
        $password = config('services.onlyoffice.docspace_password');
        
        if (empty($docspaceUrl) || empty($user) || empty($password)) {
            return null;
        }
        
        try {
            $response = Http::post("{$docspaceUrl}/api/2.0/authentication", [
                'userName' => $user,
                'password' => $password,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $token = $data['response']['token'] ?? null;
                
                if ($token) {
                    // Guardar en cache por 30 días
                    Cache::put($cacheKey, $token, 60 * 24 * 30);
                    return $token;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error autenticando en DocSpace', ['error' => $e->getMessage()]);
        }
        
        return null;
    }

    /**
     * Subir archivo a DocSpace para edición o visualización
     * 
     * @param string $mode 'edit' o 'view' - determina el tipo de acceso
     */
    public function uploadToDocSpace(Request $request, $fileId)
    {
        $file = File::findOrFail($fileId);
        $mode = $request->get('mode', 'edit'); // edit o view
        $token = $this->getDocSpaceToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo autenticar en DocSpace. Verifica la configuración.'
            ], 401);
        }
        
        $docspaceUrl = config('services.onlyoffice.docspace_url');
        $roomId = config('services.onlyoffice.docspace_room_id');
        
        if (empty($roomId)) {
            return response()->json([
                'success' => false,
                'message' => 'No se ha configurado el Room ID de DocSpace.'
            ], 400);
        }
        
        try {
            // Verificar si el archivo ya tiene un documento en DocSpace
            if ($file->hasDocSpaceDocument()) {
                // Verificar que el documento aún existe en DocSpace
                $existsResponse = Http::withHeaders([
                    'Authorization' => $token,
                ])->get("{$docspaceUrl}/api/2.0/files/file/{$file->docspace_id}");
                
                if ($existsResponse->successful()) {

                    // Actualizar fecha de último acceso
                    $file->touchDocSpaceAccess();
                    
                    // Determinar el modo del SDK: viewer o editor
                    $sdkMode = $mode === 'view' ? 'viewer' : 'editor';
                    
                    // Regenerar embed data con los datos guardados
                    $embedData = $this->generateEmbedSdk(
                        $file->docspace_id, 
                        $file->docspace_request_token, 
                        $docspaceUrl,
                        $sdkMode
                    );
                    
                    // Determinar URL según el modo
                    $accessUrl = $mode === 'view' 
                        ? ($file->link_view ?? "{$docspaceUrl}/doceditor?fileId={$file->docspace_id}&action=view")
                        : ($file->link_edit ?? "{$docspaceUrl}/doceditor?fileId={$file->docspace_id}");
                    
                    return response()->json([
                        'success' => true,
                        'docspace_file_id' => $file->docspace_id,
                        'edit_url' => $file->link_edit ?? "{$docspaceUrl}/doceditor?fileId={$file->docspace_id}",
                        'view_url' => $file->link_view ?? "{$docspaceUrl}/doceditor?fileId={$file->docspace_id}&action=view",
                        'access_url' => $accessUrl,
                        'public_url' => $file->public_url,
                        'is_editable' => true,
                        'mode' => $mode,
                        'link_message' => 'Usando documento existente en DocSpace',
                        'embed' => $embedData,
                        'reused' => true,
                    ]);
                } else {
                    $file->clearDocSpaceData();
                }
            }
            
            // Leer el archivo local
            $filePath = storage_path('app/public/gestion-documental/' . $file->original_name);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo no existe en el servidor.'
                ], 404);
            }
            
            // Subir a DocSpace
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->attach(
                'file',
                file_get_contents($filePath),
                $file->name . '.' . $file->extension
            )->post("{$docspaceUrl}/api/2.0/files/{$roomId}/upload");
            
            if ($response->successful()) {
                $data = $response->json();
                $docspaceFileId = $data['response']['id'] ?? null;
                
                if ($docspaceFileId) {
                    // Obtener enlace público y requestToken
                    $linkData = $this->createPublicEditLink($docspaceFileId, $token, $docspaceUrl);
                    
                    // Determinar el modo del SDK: viewer o editor
                    $sdkMode = $mode === 'view' ? 'viewer' : 'editor';
                    
                    // Generar URL del SDK para embeber
                    $embedData = $this->generateEmbedSdk($docspaceFileId, $linkData['requestToken'] ?? null, $docspaceUrl, $sdkMode);
                    
                    // Construir URLs
                    $editUrl = $linkData['url'] ?? "{$docspaceUrl}/doceditor?fileId={$docspaceFileId}";
                    $viewUrl = "{$docspaceUrl}/doceditor?fileId={$docspaceFileId}&action=view";
                    
                    // Guardar en la base de datos en lugar del cache
                    $file->update([
                        'docspace_id' => $docspaceFileId,
                        'link_edit' => $editUrl,
                        'link_view' => $viewUrl,
                        'public_url' => $linkData['url'],
                        'docspace_request_token' => $linkData['requestToken'] ?? null,
                        'ult_date_docspace' => now(),
                    ]);
                    
                    // También guardar en cache para compatibilidad temporal
                    Cache::put("docspace_file_{$fileId}", [
                        'docspace_id' => $docspaceFileId,
                        'local_file_id' => $fileId,
                        'public_link' => $linkData['url'],
                        'request_token' => $linkData['requestToken'] ?? null,
                        'is_editable' => $linkData['isEditable'],
                        'uploaded_at' => now(),
                    ], 60 * 24); // 24 horas
                    
                    // Determinar URL según el modo
                    $accessUrl = $mode === 'view' ? $viewUrl : $editUrl;
                    
                    return response()->json([
                        'success' => true,
                        'docspace_file_id' => $docspaceFileId,
                        'edit_url' => $editUrl,
                        'view_url' => $viewUrl,
                        'access_url' => $accessUrl,
                        'public_url' => $linkData['url'],
                        'is_editable' => $linkData['isEditable'],
                        'mode' => $mode,
                        'link_message' => $linkData['message'],
                        'embed' => $embedData,
                        'reused' => false,
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al subir archivo a DocSpace.',
                'details' => $response->json()
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Error subiendo a DocSpace', [
                'fileId' => $fileId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear enlace público de edición para un archivo en DocSpace
     * 
     * El enlace de edición pública usa el formato: /doceditor?fileId={fileId}&share={roomShareToken}
     * El shareToken se obtiene del enlace de la Room, no del archivo.
     */
    protected function createPublicEditLink(string $docspaceFileId, string $token, string $docspaceUrl): array
    {
        $roomId = config('services.onlyoffice.docspace_room_id');
        
        try {
            // Obtener el enlace compartido de la ROOM (no del archivo)
            // Este enlace tiene permisos de edición para todos los archivos en la room
            $roomLinkResponse = Http::withHeaders([
                'Authorization' => $token,
            ])->get("{$docspaceUrl}/api/2.0/files/rooms/{$roomId}/links");
            
            if ($roomLinkResponse->successful()) {
                $roomData = $roomLinkResponse->json();
                $roomLinks = $roomData['response'] ?? [];
                
                // Buscar un enlace con permisos de edición (access 1 o 2)
                foreach ($roomLinks as $link) {
                    $access = $link['access'] ?? null;
                    $requestToken = $link['sharedTo']['requestToken'] ?? null;
                    
                    // access: 1 = Full Access, 2 = Editing
                    if (in_array($access, [1, 2]) && $requestToken) {
                        // Construir URL de edición directa
                        $editUrl = "{$docspaceUrl}/doceditor?fileId={$docspaceFileId}&share=" . urlencode($requestToken);
                        
                        return [
                            'url' => $editUrl,
                            'requestToken' => $requestToken,
                            'access' => $access,
                            'linkType' => 'room_share',
                            'isEditable' => true,
                            'canEditAccess' => true,
                            'message' => 'Enlace de edición pública (usando token de room)',
                        ];
                    }
                }
                
                // Si no hay enlace con edición, intentar obtener el primario de la room
                $primaryLink = $roomLinks[0] ?? null;
                if ($primaryLink) {
                    $requestToken = $primaryLink['sharedTo']['requestToken'] ?? null;
                    if ($requestToken) {
                        $editUrl = "{$docspaceUrl}/doceditor?fileId={$docspaceFileId}&share=" . urlencode($requestToken);
                        
                        return [
                            'url' => $editUrl,
                            'requestToken' => $requestToken,
                            'access' => $primaryLink['access'] ?? null,
                            'linkType' => 'room_share',
                            'isEditable' => in_array($primaryLink['access'] ?? 0, [1, 2]),
                            'canEditAccess' => false,
                            'message' => 'Enlace usando token de room',
                        ];
                    }
                }
            }
            
            // Fallback: obtener el link del archivo
            $getLinksResponse = Http::withHeaders([
                'Authorization' => $token,
            ])->get("{$docspaceUrl}/api/2.0/files/file/{$docspaceFileId}/link");
            
            if ($getLinksResponse->successful()) {
                $data = $getLinksResponse->json();
                $response = $data['response'] ?? [];
                
                $requestToken = $response['sharedTo']['requestToken'] ?? null;
                $access = $response['access'] ?? null;
                
                if ($requestToken) {
                    // Usar formato doceditor con share token del archivo
                    $editUrl = "{$docspaceUrl}/doceditor?fileId={$docspaceFileId}&share=" . urlencode($requestToken);
                    
                    return [
                        'url' => $editUrl,
                        'requestToken' => $requestToken,
                        'access' => $access,
                        'linkType' => 'file_share',
                        'isEditable' => in_array($access, [1, 2]),
                        'canEditAccess' => $response['canEditAccess'] ?? false,
                        'message' => in_array($access, [1, 2]) 
                            ? 'Enlace con permisos de edición' 
                            : 'Enlace de solo lectura',
                    ];
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo enlace público en DocSpace', [
                'fileId' => $docspaceFileId,
                'error' => $e->getMessage()
            ]);
        }
        
        return [
            'url' => null,
            'requestToken' => null,
            'access' => null,
            'linkType' => null,
            'isEditable' => false,
            'canEditAccess' => false,
            'message' => 'No se pudo obtener el enlace público',
        ];
    }

    /**
     * Generar datos para embeber el editor/visor con el SDK de DocSpace
     * 
     * @param string $sdkMode 'editor' para edición, 'viewer' para solo lectura
     */
    protected function generateEmbedSdk(string $docspaceFileId, ?string $requestToken, string $docspaceUrl, string $sdkMode = 'editor'): array
    {
        if (!$requestToken) {
            return [
                'available' => false,
                'message' => 'No hay token disponible para embeber',
            ];
        }
        
        // URL base del SDK
        $sdkVersion = '2.1.0';
        $sdkBaseUrl = "{$docspaceUrl}/static/scripts/sdk/{$sdkVersion}/api.js";
        
        // Parámetros para el SDK
        $params = [
            'src'           => $docspaceUrl,
            'mode'          => $sdkMode, // 'editor' o 'viewer'
            'width'         => '100%',
            'height'        => '100%',
            'frameId'       => 'ds-frame',
            'init'          => 'true',
            'id'            => $docspaceFileId,
            'requestToken'  => $requestToken,
        ];
        
        // Solo agregar editorType=embedded para modo viewer
        // En modo editor, no se debe incluir para permitir edición completa
        if ($sdkMode === 'viewer') {
            $params['editorType'] = 'embedded';
        }
        
        // Construir URL completa del SDK
        $sdkUrl = $sdkBaseUrl . '?' . http_build_query($params);
        
        return [
            'available'     => true,
            'sdk_url'       => $sdkUrl,
            'sdk_base_url'  => $sdkBaseUrl,
            'file_id'       => $docspaceFileId,
            'request_token' => $requestToken,
            'docspace_url'  => $docspaceUrl,
            'frame_id'      => 'ds-frame',
            'mode'          => $sdkMode,
        ];
    }

    /**
     * Verifica si un archivo puede ser abierto
     */
    public function canOpen(string $extension): bool
    {
        return isset($this->documentTypes[strtolower($extension)]);
    }

    /**
     * Verifica si un archivo puede ser editado
     */
    public function canEdit(string $extension): bool
    {
        return in_array(strtolower($extension), $this->editableExtensions);
    }

    /**
     * Crear un nuevo documento (Excel, Word o PowerPoint) y abrirlo en OnlyOffice
     */
    public function createDocument(Request $request)
    {
        $request->validate([
            'type' => 'required|in:xlsx,docx,pptx',
            'name' => 'required|string|max:255',
            'folder_id' => 'nullable|integer',
            'roles' => 'nullable|array',
            'usuarios' => 'nullable|array',
        ]);

        $type = $request->input('type');
        $name = $request->input('name');
        $folderId = $request->input('folder_id');
        $roles = $request->input('roles', []);
        $usuarios = $request->input('usuarios', []);

        // Obtener el template correspondiente
        $templatePath = storage_path("app/templates/blank.{$type}");
        
        if (!file_exists($templatePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Template no encontrado. Contacte al administrador.'
            ], 500);
        }

        // Generar nombre único para el archivo
        $uniqueName = \Illuminate\Support\Str::uuid() . '.' . $type;
        $destinationPath = storage_path('app/public/gestion-documental/' . $uniqueName);

        // Copiar el template
        if (!copy($templatePath, $destinationPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el archivo.'
            ], 500);
        }

        // Determinar mime type
        $mimeTypes = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        // Crear registro en la base de datos
        $file = File::create([
            'name' => $name,
            'original_name' => $uniqueName,
            'size' => filesize($destinationPath),
            'mime_type' => $mimeTypes[$type],
            'extension' => $type,
            'user_id' => Auth::id(),
            'carpeta_id' => $folderId > 0 ? $folderId : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear registro en historial
        FileHistoral::create([
            'file_id' => $file->id,
            'user_id' => Auth::id(),
            'original_name' => $uniqueName,
            'created_at' => now(),
        ]);

        // Asignar permisos a usuarios
        $usuarios[] = Auth::id(); // El creador siempre tiene acceso
        $usuarios = array_unique($usuarios);
        
        foreach ($usuarios as $userId) {
            \App\Models\Repositorio\FileUsuario::create([
                'file_id' => $file->id,
                'user_id' => $userId,
            ]);
        }

        // Asignar permisos a roles
        foreach ($roles as $roleId) {
            \App\Models\Repositorio\FileUsuario::create([
                'file_id' => $file->id,
                'role_id' => $roleId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Documento creado exitosamente.',
            'file_id' => $file->id,
            'redirect_url' => route('onlyoffice.editor', ['fileId' => $file->id, 'mode' => 'edit']),
        ]);
    }
}

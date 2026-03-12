<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Repositorio\File;
use Carbon\Carbon;

class CleanDocSpaceFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docspace:clean 
                            {--days=2 : Días de inactividad antes de sincronizar y eliminar}
                            {--dry-run : Solo mostrar qué archivos se afectarían}
                            {--force : Forzar la operación sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza y elimina archivos de DocSpace que no han sido accedidos en X días';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        $this->info("Buscando archivos en DocSpace sin actividad en los últimos {$days} días...");
        
        // Buscar archivos con docspace_id que no han sido accedidos
        $cutoffDate = date('Y-m-d H:i', strtotime("-{$days} days"));
        
        $files = File::whereNotNull('docspace_id')
            ->where(function ($query) use ($cutoffDate) {
                $query->where('ult_date_docspace', '<', $cutoffDate)
                    ->orWhereNull('ult_date_docspace');
            })
            ->get();
        
        if ($files->isEmpty()) {
            $this->info('No se encontraron archivos para limpiar.');
            return Command::SUCCESS;
        }
        
        $this->info("Se encontraron {$files->count()} archivos para procesar.");
        
        if ($dryRun) {
            $this->warn('Modo DRY-RUN: No se realizarán cambios.');
            $this->table(
                ['ID', 'Nombre', 'DocSpace ID', 'Último acceso'],
                $files->map(fn($f) => [
                    $f->id,
                    $f->name,
                    $f->docspace_id,
                    $f->ult_date_docspace ?? 'Nunca',
                ])->toArray()
            );
            return Command::SUCCESS;
        }
        
        if (!$force && !$this->confirm('¿Desea continuar con la sincronización y eliminación?')) {
            $this->info('Operación cancelada.');
            return Command::SUCCESS;
        }
        
        // Obtener token de DocSpace
        $token = $this->getDocSpaceToken();
        if (!$token) {
            $this->error('No se pudo autenticar en DocSpace.');
            return Command::FAILURE;
        }
        
        $docspaceUrl = config('services.onlyoffice.docspace_url');
        $successCount = 0;
        $errorCount = 0;
        
        $progressBar = $this->output->createProgressBar($files->count());
        $progressBar->start();
        
        foreach ($files as $file) {
            try {
                // Sincronizar archivo desde DocSpace
                $syncResult = $this->syncFile($file, $token, $docspaceUrl);
                
                if ($syncResult['success']) {
                    // Eliminar de DocSpace
                    $this->deleteFromDocSpace($file->docspace_id, $token, $docspaceUrl);
                    
                    // Limpiar datos de DocSpace en BD
                    $file->clearDocSpaceData();
                    
                    $successCount++;

                } else {
                    $errorCount++;
                    Log::warning('Error sincronizando archivo de DocSpace', [
                        'file_id' => $file->id,
                        'error' => $syncResult['message'],
                    ]);
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Excepción procesando archivo', [
                    'file_id' => $file->id,
                    'error' => $e->getMessage(),
                ]);
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Proceso completado:");
        $this->info("  - Archivos sincronizados y eliminados: {$successCount}");
        if ($errorCount > 0) {
            $this->warn("  - Errores: {$errorCount}");
        }
        
        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
    
    /**
     * Obtener token de DocSpace
     */
    protected function getDocSpaceToken(): ?string
    {
        $cacheKey = 'docspace_token_system';
        
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
                    Cache::put($cacheKey, $token, 60 * 23);
                    return $token;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error autenticando en DocSpace', ['error' => $e->getMessage()]);
        }
        
        return null;
    }
    
    /**
     * Sincronizar archivo desde DocSpace
     */
    protected function syncFile(File $file, string $token, string $docspaceUrl): array
    {
        try {
            // Forzar guardado antes de descargar
            Http::withHeaders([
                'Authorization' => $token,
            ])->put("{$docspaceUrl}/api/2.0/files/file/{$file->docspace_id}/forcesave");
            
            sleep(1); // Esperar un segundo
            
            // Obtener información del archivo
            $fileInfoResponse = Http::withHeaders([
                'Authorization' => $token,
            ])->get("{$docspaceUrl}/api/2.0/files/file/{$file->docspace_id}");
            
            if (!$fileInfoResponse->successful()) {
                return [
                    'success' => false,
                    'message' => 'No se pudo obtener información del archivo en DocSpace',
                ];
            }
            
            $fileInfo = $fileInfoResponse->json();
            $responseData = $fileInfo['response'] ?? $fileInfo;
            
            // Obtener URL de descarga
            $downloadUrl = $responseData['viewUrl'] ?? null;
            if (!$downloadUrl) {
                $downloadUrl = "{$docspaceUrl}/products/files/httphandlers/filehandler.ashx?action=download&fileid={$file->docspace_id}";
            }
            
            // Descargar el archivo
            $downloadResponse = Http::withHeaders([
                'Authorization' => $token,
            ])->withOptions([
                'timeout' => 120,
            ])->get($downloadUrl);
            
            if (!$downloadResponse->successful()) {
                return [
                    'success' => false,
                    'message' => 'Error descargando archivo de DocSpace',
                ];
            }
            
            $content = $downloadResponse->body();
            
            // Verificar que no sea un error JSON
            if (str_starts_with(trim($content), '{') || str_starts_with(trim($content), '[')) {
                $possibleError = json_decode($content, true);
                if (isset($possibleError['error']) || isset($possibleError['message'])) {
                    return [
                        'success' => false,
                        'message' => $possibleError['message'] ?? 'Error en respuesta de DocSpace',
                    ];
                }
            }
            
            if (empty($content)) {
                return [
                    'success' => false,
                    'message' => 'El archivo descargado está vacío',
                ];
            }
            
            // Guardar localmente
            $path = 'gestion-documental/' . $file->original_name;
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $content);
            
            // Actualizar metadatos
            $file->size = strlen($content);
            $file->updated_at = now();
            $file->save();
            
            return [
                'success' => true,
                'message' => 'Archivo sincronizado correctamente',
                'size' => strlen($content),
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Eliminar archivo de DocSpace
     * 
     * Usa múltiples métodos para asegurar la eliminación
     */
    protected function deleteFromDocSpace(string $docspaceFileId, string $token, string $docspaceUrl): bool
    {
        try {
            // Método 1: DELETE directo con parámetros de eliminación permanente
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->delete("{$docspaceUrl}/api/2.0/files/file/{$docspaceFileId}", [
                'deleteAfter' => true,
                'immediately' => true,
            ]);

            if ($response->successful()) {
                return true;
            }
            
            // Método 2: Batch delete operation
            $batchResponse = Http::withHeaders([
                'Authorization' => $token,
            ])->put("{$docspaceUrl}/api/2.0/files/fileops/delete", [
                'fileIds' => [$docspaceFileId],
                'deleteAfter' => true,
                'immediately' => true,
            ]);
            
            
            if ($batchResponse->successful()) {
                sleep(1); // Esperar a que se procese
                return true;
            }
            
            Log::warning('No se pudo eliminar archivo de DocSpace - todos los métodos fallaron', [
                'docspaceFileId' => $docspaceFileId,
            ]);
            
            return false;
            
        } catch (\Exception $e) {
            Log::warning('Excepción al eliminar archivo de DocSpace', [
                'docspaceFileId' => $docspaceFileId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('repositorio_files', function (Blueprint $table) {
            // ID del documento en DocSpace
            $table->string('docspace_id')->nullable()->after('carpeta_id');
            // Enlace de edición
            $table->text('link_edit')->nullable()->after('docspace_id');
            // Enlace de visualización
            $table->text('link_view')->nullable()->after('link_edit');
            // URL pública
            $table->text('public_url')->nullable()->after('link_view');
            // Request token de DocSpace (para embeber)
            $table->string('docspace_request_token')->nullable()->after('public_url');
            
            // Índice para búsqueda rápida por docspace_id
            $table->index('docspace_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repositorio_files', function (Blueprint $table) {
            $table->dropIndex(['docspace_id']);
            $table->dropColumn([
                'docspace_id',
                'link_edit',
                'link_view',
                'public_url',
                'docspace_request_token',
            ]);
        });
    }
};

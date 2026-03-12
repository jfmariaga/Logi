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
            // Última fecha de acceso en DocSpace (visualización o edición)
            $table->timestamp('ult_date_docspace')->nullable()->after('docspace_request_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repositorio_files', function (Blueprint $table) {
            $table->dropColumn('ult_date_docspace');
        });
    }
};

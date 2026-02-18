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
        Schema::table('carpetas_por_usuarios', function (Blueprint $table) {
            // Agregar columna rol_id nullable
            $table->bigInteger('user_id')->nullable()->change();
            $table->bigInteger('role_id')->nullable()->after('user_id');
            // Eliminar timestamps
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carpetas_por_usuarios', function (Blueprint $table) {
            // Restaurar timestamps
            $table->timestamps();
            // Eliminar columna rol_id
            $table->dropColumn('role_id');
        });
    }
};

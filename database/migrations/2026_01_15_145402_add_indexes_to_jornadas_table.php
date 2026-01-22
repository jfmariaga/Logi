<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jornadas', function (Blueprint $table) {

            $table->index('fin');
            $table->index('inicio');
            $table->index('sede_id');
            $table->index('sede_salida_id');
            $table->index('fuera_sede');

            // combinados para dashboard
            $table->index(['fin', 'sede_id']);
            $table->index(['fin', 'fuera_sede']);
            $table->index(['sede_id', 'sede_salida_id']);
        });
    }

    public function down(): void
    {
        Schema::table('jornadas', function (Blueprint $table) {

            $table->dropIndex(['fin']);
            $table->dropIndex(['inicio']);
            $table->dropIndex(['sede_id']);
            $table->dropIndex(['sede_salida_id']);
            $table->dropIndex(['fuera_sede']);

            $table->dropIndex(['fin', 'sede_id']);
            $table->dropIndex(['fin', 'fuera_sede']);
            $table->dropIndex(['sede_id', 'sede_salida_id']);
        });
    }
};

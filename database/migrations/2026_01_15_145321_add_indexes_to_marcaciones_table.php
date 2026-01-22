<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('marcaciones', function (Blueprint $table) {

            // filtros principales
            $table->index('fecha_hora');
            $table->index('user_id');
            $table->index('sede_id');
            $table->index('en_sitio');

            // índice compuesto para consultas típicas
            $table->index(['fecha_hora', 'user_id']);
            $table->index(['fecha_hora', 'sede_id']);
        });
    }

    public function down(): void
    {
        Schema::table('marcaciones', function (Blueprint $table) {

            $table->dropIndex(['fecha_hora']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['sede_id']);
            $table->dropIndex(['en_sitio']);

            $table->dropIndex(['fecha_hora', 'user_id']);
            $table->dropIndex(['fecha_hora', 'sede_id']);
        });
    }
};

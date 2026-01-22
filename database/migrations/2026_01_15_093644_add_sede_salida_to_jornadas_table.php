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
        Schema::table('jornadas', function (Blueprint $table) {

            $table->foreignId('sede_salida_id')
                ->nullable()
                ->after('sede_id')
                ->constrained('sedes');

            $table->boolean('salida_fuera_sede')->default(false)->after('fuera_sede');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jornadas', function (Blueprint $table) {
            //
        });
    }
};

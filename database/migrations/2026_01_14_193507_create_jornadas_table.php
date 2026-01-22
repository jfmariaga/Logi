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
        Schema::create('jornadas', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sede_id')
                ->nullable()
                ->constrained('sedes')
                ->nullOnDelete();

            // ⏱️ tiempos
            $table->dateTime('inicio');
            $table->dateTime('fin')->nullable();

            // duración en minutos (se calcula al cerrar)
            $table->integer('minutos_trabajados')->nullable();

            // estado
            $table->boolean('cerrada')->default(false);

            // marcaciones sospechosas / fuera de sede
            $table->boolean('fuera_sede')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jornadas');
    }
};

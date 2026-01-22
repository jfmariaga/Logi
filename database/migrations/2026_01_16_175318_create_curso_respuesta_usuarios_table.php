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
        Schema::create('curso_respuestas_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intento_id')->constrained('curso_intentos')->cascadeOnDelete();
            $table->foreignId('pregunta_id')->constrained('curso_preguntas')->cascadeOnDelete();
            $table->foreignId('respuesta_id')->constrained('curso_respuestas')->cascadeOnDelete();
            $table->boolean('es_correcta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_respuesta_usuarios');
    }
};

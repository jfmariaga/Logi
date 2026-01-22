<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curso_progresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_materiales_completados')->nullable();
            $table->timestamp('fecha_finalizado')->nullable();
            $table->timestamps();
            $table->unique(['curso_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curso_progresos');
    }
};

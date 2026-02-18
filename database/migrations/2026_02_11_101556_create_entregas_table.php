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
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->enum('tipo', ['epp', 'dotacion']);
            $table->foreignId('responsable_id')
                ->constrained('users');
            $table->text('observaciones')->nullable();
            $table->enum('estado', [
                'creada',
                'enviada',
                'pendiente_firma',
                'firmada',
                'finalizada'
            ])->default('creada');
            $table->date('fecha_envio')->nullable();
            $table->date('fecha_recepcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};

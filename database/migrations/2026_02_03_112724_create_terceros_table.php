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
        Schema::create('terceros', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_identificacion')->nullable();
            $table->string('identificacion')->unique();
            $table->string('password');
            $table->enum('tipo', ['juridica', 'natural']);
            $table->enum('estado', ['en_proceso', 'rechazado', 'aprobado','enviado'])->default('en_proceso');
            $table->integer('progreso')->default(0);
            $table->timestamp('ultimo_acceso')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terceros');
    }
};

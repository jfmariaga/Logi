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
        Schema::create('marcaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()
                ->cascadeOnDelete();;
            $table->foreignId('sede_id')->constrained()
                ->cascadeOnDelete();;
            $table->enum('tipo', ['entrada', 'salida']);
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->integer('distancia_metros');
            $table->boolean('en_sitio');
            $table->ipAddress('ip');
            $table->string('user_agent')->nullable();
            $table->timestamp('fecha_hora');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marcacions');
    }
};

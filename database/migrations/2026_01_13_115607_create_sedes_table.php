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
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');

            // 📍 Ubicación
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->integer('radio_metros')->default(150);

            // 📞 Datos de contacto
            $table->string('contacto')->nullable();          // Nombre persona contacto
            $table->string('telefono_contacto')->nullable(); // Teléfono
            $table->string('direccion')->nullable();         // Dirección física

            // ⚙️ Estado
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};

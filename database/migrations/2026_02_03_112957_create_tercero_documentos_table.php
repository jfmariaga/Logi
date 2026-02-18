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
        Schema::create('tercero_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tercero_id')->constrained()->cascadeOnDelete();
            $table->string('tipo_documento');
            $table->string('archivo')->nullable();
            $table->boolean('obligatorio')->default(false);
            $table->boolean('cargado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tercero_documentos');
    }
};

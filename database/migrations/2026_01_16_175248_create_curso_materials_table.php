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
        Schema::create('curso_materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['video', 'pdf', 'ppt', 'link']);
            $table->string('titulo');
            $table->string('archivo_path')->nullable(); // para pdf, ppt, video
            $table->string('url')->nullable(); // para links o videos externos
            $table->unsignedInteger('orden')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_materials');
    }
};

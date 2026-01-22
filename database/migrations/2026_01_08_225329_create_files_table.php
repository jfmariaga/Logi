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
        Schema::create('repositorio_files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('original_name')->nullable();
            $table->string('path')->nullable();
            $table->string('extension')->nullable();
            $table->string('mime_type')->nullable();
            $table->decimal('size', 10, 2)->nullable(); // En KB
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('carpeta_id')->nullable();
            $table->timestamps();
            $table->dateTime('fecha_delete')->nullable(); 
            $table->bigInteger('id_user_delete')->nullable(); 
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('carpeta_id')->references('id')->on('repositorio_carpetas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositorio_files');
    }
};

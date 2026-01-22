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
        Schema::create('repositorio_carpetas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent')->nullable(); 
            $table->string('nombre');        
            $table->text('descripcion')->nullable();
            $table->tinyInteger('privada')->nullable(); 
            $table->tinyInteger('status')->nullable(); 
            $table->date('fecha_delete')->nullable(); 
            $table->bigInteger('id_user_delete')->nullable(); 
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositorio_carpetas');
    }
};

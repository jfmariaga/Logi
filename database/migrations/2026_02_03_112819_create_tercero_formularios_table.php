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
        Schema::create('tercero_formularios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tercero_id')->constrained()->cascadeOnDelete();
            $table->string('seccion');
            $table->string('campo');
            $table->text('valor')->nullable();
            $table->boolean('obligatorio')->default(false);
            $table->string('tipo_campo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tercero_formularios');
    }
};

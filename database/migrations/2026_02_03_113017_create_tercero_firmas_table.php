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
        Schema::create('tercero_firmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tercero_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['digital', 'escaneada']);
            $table->string('archivo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tercero_firmas');
    }
};

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
        Schema::create('entrega_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entrega_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->constrained();

            $table->string('talla')->nullable();

            $table->integer('cantidad');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_items');
    }
};

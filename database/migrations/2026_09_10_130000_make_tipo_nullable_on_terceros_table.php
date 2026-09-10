<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->enum('tipo', ['juridica', 'natural'])->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->enum('tipo', ['juridica', 'natural'])->nullable(false)->change();
        });
    }
};

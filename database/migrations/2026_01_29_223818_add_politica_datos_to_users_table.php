<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('acepto_politica_datos')
                ->default(false)
                ->after('remember_token');

            $table->timestamp('fecha_acepto_politica')
                ->nullable()
                ->after('acepto_politica_datos');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'acepto_politica_datos',
                'fecha_acepto_politica'
            ]);
        });
    }
};

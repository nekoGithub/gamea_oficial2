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
        Schema::table('servidores', function (Blueprint $table) {
            $table->enum('disponibilidad_interna', ['ACTIVO', 'INACTIVO', 'DESCONOCIDO'])
                ->default('DESCONOCIDO')->after('estado');
            $table->enum('disponibilidad_externa', ['ACTIVO', 'INACTIVO', 'DESCONOCIDO'])
                ->default('DESCONOCIDO')->after('disponibilidad_interna');
            $table->timestamp('ultima_verificacion')->nullable()->after('disponibilidad_externa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servidores', function (Blueprint $table) {
            $table->dropColumn([
                'disponibilidad_interna',
                'disponibilidad_externa',
                'ultima_verificacion'
            ]);
        });
    }
};

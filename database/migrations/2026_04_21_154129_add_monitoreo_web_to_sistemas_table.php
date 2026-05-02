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
        Schema::table('sistemas', function (Blueprint $table) {
            $table->enum('disponibilidad_web', ['ACTIVO', 'INACTIVO', 'DESCONOCIDO'])
                ->default('DESCONOCIDO')->after('estado');
            $table->integer('http_status')->nullable()->after('disponibilidad_web');
            $table->integer('tiempo_respuesta')->nullable()->after('http_status');
            $table->timestamp('ultima_verificacion_web')->nullable()->after('tiempo_respuesta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sistemas', function (Blueprint $table) {
            $table->dropColumn([
                'disponibilidad_web',
                'http_status',
                'tiempo_respuesta',
                'ultima_verificacion_web'
            ]);
        });
    }
};

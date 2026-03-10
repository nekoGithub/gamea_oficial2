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
        Schema::create('tecnologias', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100);
            $table->string('version', 50);
            $table->text('descripcion')->nullable();
            $table->string('url_documentacion', 255)->nullable();
            $table->date('fecha_lanzamiento')->nullable();
            $table->date('fecha_fin_soporte')->nullable();
            $table->enum('tipo', ['backend', 'frontend', 'otros/librerias']);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tecnologias');
    }
};

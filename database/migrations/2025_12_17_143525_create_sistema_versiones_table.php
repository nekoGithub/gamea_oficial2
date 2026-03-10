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
        Schema::create('sistema_versiones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sistema_id')->constrained('sistemas')->cascadeOnDelete();
            $table->string('numero_version', 50);
            $table->text('descripcion')->nullable();
            $table->string('imagen', 255)->nullable();
            $table->string('codigo_fuente', 255)->nullable();
            $table->string('manual_tecnico', 255)->nullable();
            $table->string('manual_usuario', 255)->nullable();
            $table->date('fecha_lanzamiento');
            $table->foreignId('publicado_por')->constrained('users');
            $table->enum('estado', ['estable', 'beta', 'deprecated']);
            $table->boolean('es_actual')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_versiones');
    }
};

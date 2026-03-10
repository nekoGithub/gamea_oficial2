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
        Schema::create('sistemas', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);
            $table->string('sigla', 20)->nullable();
            $table->string('dominio', 150);
            $table->json('tipo')->default('["interno"]');
            $table->foreignId('unidad_id')->constrained('unidades');
            $table->foreignId('ssl_id')->nullable()->constrained('ssls');
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
        Schema::dropIfExists('sistemas');
    }
};

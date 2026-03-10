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
        Schema::create('servidores', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);
            $table->string('ip_interna', 45);
            $table->string('ip_externa', 45)->nullable();
            $table->string('mac_address', 20)->nullable();
            $table->foreignId('sistema_operativo_id')->constrained('sistemas_operativos');
            $table->enum('tipo_servidor', ['físico', 'virtual']);
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
        Schema::dropIfExists('servidores');
    }
};

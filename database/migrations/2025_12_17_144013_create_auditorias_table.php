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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();

            // Usuario que realizó la acción
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Acción realizada
            $table->enum('accion', [
                'login',
                'logout',
                'created',
                'updated',
                'deleted',
                'restored'
            ]);

            // Módulo/Entidad afectada
            $table->string('modulo', 50); // sistemas, credenciales, ssls, etc.

            // ID del registro afectado (polimórfico)
            $table->unsignedBigInteger('entidad_id')->nullable();

            // Descripción de la acción
            $table->text('descripcion');

            // Valores anteriores (JSON)
            $table->json('valores_anteriores')->nullable();

            // Valores nuevos (JSON)
            $table->json('valores_nuevos')->nullable();

            // IP desde donde se realizó la acción
            $table->string('ip_address', 45)->nullable();

            // User Agent
            $table->text('user_agent')->nullable();

            $table->timestamps();            

            // Índices para optimizar búsquedas
            $table->index(['user_id', 'created_at']);
            $table->index(['modulo', 'entidad_id']);
            $table->index('accion');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};

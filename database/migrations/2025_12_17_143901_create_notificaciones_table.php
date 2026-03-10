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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sistema_version_id')->constrained('sistema_versiones')->cascadeOnDelete();
            $table->dateTime('fecha');
            $table->enum('estado', ['pendiente', 'enviado', 'fallido']);
            $table->text('mensaje');
            $table->foreignId('usuario_enviado')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};

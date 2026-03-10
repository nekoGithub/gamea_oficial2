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
        Schema::create('ssls', function (Blueprint $table) {
            $table->id();

            $table->string('emisor', 150)->nullable();
            $table->string('archivo_ssl')->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_expiracion');
            $table->enum('estado', ['valido', 'proximo_vencer', 'vencido']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ssls');
    }
};

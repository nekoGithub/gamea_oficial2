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
        Schema::create('sistema_version_servidores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sistema_version_id')->constrained('sistema_versiones')->cascadeOnDelete();
            $table->foreignId('servidor_id')->constrained('servidores');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_version_servidores');
    }
};

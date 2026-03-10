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
        Schema::create('documento_sistema_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos')->cascadeOnDelete();
            $table->foreignId('sistema_version_id')->constrained('sistema_versiones')->cascadeOnDelete();
            $table->string('archivo', 255);
            $table->timestamps();

            $table->index(['documento_id', 'sistema_version_id']);
            $table->unique(['documento_id', 'sistema_version_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_sistema_versiones');
    }
};

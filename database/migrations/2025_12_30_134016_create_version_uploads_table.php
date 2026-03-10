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
        Schema::create('version_uploads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sistema_id')->constrained('sistemas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('numero_version', 50);

            // Estado del proceso
            $table->enum('estado', ['pendiente', 'procesando', 'completado', 'error'])->default('pendiente');
            $table->integer('progreso')->default(0); // 0-100
            $table->text('error_message')->nullable();

            // Datos del formulario (JSON)
            $table->json('data')->nullable();

            // Metadatos de chunks
            $table->string('chunk_identifier')->nullable();

            $table->string('file_name')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->string('file_hash')->nullable();

            $table->integer('total_chunks')->nullable();
            $table->integer('chunks_received')->default(0);

            $table->integer('last_chunk_completed')->default(-1);

            // Manual Técnico - Chunks
            $table->string('manual_tecnico_name')->nullable()->after('temp_manual_tecnico');
            $table->bigInteger('manual_tecnico_size')->nullable();
            $table->string('manual_tecnico_identifier')->nullable();
            $table->integer('manual_tecnico_total_chunks')->nullable();
            $table->integer('manual_tecnico_chunks_received')->default(0);

            // Manual Usuario - Chunks
            $table->string('manual_usuario_name')->nullable()->after('temp_manual_usuario');
            $table->bigInteger('manual_usuario_size')->nullable();
            $table->string('manual_usuario_identifier')->nullable();
            $table->integer('manual_usuario_total_chunks')->nullable();
            $table->integer('manual_usuario_chunks_received')->default(0);

            // Rutas temporales
            $table->string('temp_codigo_fuente')->nullable();
            $table->string('temp_manual_tecnico')->nullable();
            $table->string('temp_manual_usuario')->nullable();
            $table->string('temp_imagen')->nullable();

            $table->timestamps();

            $table->index(['estado', 'created_at']);
            $table->index('chunk_identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('version_uploads');
    }
};

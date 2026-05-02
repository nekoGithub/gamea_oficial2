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
        Schema::table('version_uploads', function (Blueprint $table) {
            $table->string('temp_archivo_bd')->nullable()->after('temp_manual_usuario');
            $table->string('archivo_bd_identifier')->nullable()->after('manual_usuario_identifier');
            $table->integer('archivo_bd_total_chunks')->nullable();
            $table->integer('archivo_bd_chunks_received')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('version_uploads', function (Blueprint $table) {
            //
        });
    }
};

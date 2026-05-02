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
        Schema::table('sistema_versiones', function (Blueprint $table) {
            $table->string('archivo_bd')->nullable()->after('manual_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sistema_versiones', function (Blueprint $table) {
            $table->dropColumn('archivo_bd');
        });
    }
};

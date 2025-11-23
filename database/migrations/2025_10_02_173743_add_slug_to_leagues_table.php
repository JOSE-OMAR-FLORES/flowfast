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
        Schema::table('leagues', function (Blueprint $table) {
            // Verificar si la columna ya existe antes de agregarla
            if (!Schema::hasColumn('leagues', 'slug')) {
                $table->string('slug')->unique()->after('name')->comment('URL amigable para páginas públicas');
            }
            if (!Schema::hasColumn('leagues', 'description')) {
                $table->text('description')->nullable()->after('slug')->comment('Descripción pública de la liga');
            }
            if (!Schema::hasColumn('leagues', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('description')->comment('Si la liga es visible públicamente');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn(['slug', 'description', 'is_public']);
        });
    }
};

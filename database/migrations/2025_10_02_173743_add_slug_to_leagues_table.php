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
            $table->string('slug')->unique()->after('name')->comment('URL amigable para páginas públicas');
            $table->text('description')->nullable()->after('slug')->comment('Descripción pública de la liga');
            $table->boolean('is_public')->default(true)->after('description')->comment('Si la liga es visible públicamente');
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

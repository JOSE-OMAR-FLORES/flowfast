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
        Schema::table('fixture_events', function (Blueprint $table) {
            // Puntos del evento (1, 2, 3 para básquet; 1 para gol en fútbol; 1 para carrera en béisbol)
            $table->integer('points')->default(1)->after('event_type');
            
            // Periodo/Set/Cuarto/Inning donde ocurrió el evento
            $table->integer('period')->nullable()->after('points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixture_events', function (Blueprint $table) {
            $table->dropColumn(['points', 'period']);
        });
    }
};

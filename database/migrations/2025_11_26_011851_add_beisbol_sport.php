<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar Béisbol si no existe
        $exists = DB::table('sports')->where('slug', 'beisbol')->exists();
        
        if (!$exists) {
            DB::table('sports')->insert([
                'name' => 'Béisbol',
                'slug' => 'beisbol',
                'players_per_team' => 9,
                'match_duration' => 180, // Aproximado en minutos
                'scoring_system' => json_encode([
                    'win' => 2,
                    'loss' => 0,
                ]),
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('sports')->where('slug', 'beisbol')->delete();
    }
};

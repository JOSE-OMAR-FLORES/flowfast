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
        Schema::table('game_matches', function (Blueprint $table) {
            $table->boolean('is_friendly')->default(false)->after('status');
            $table->decimal('home_team_fee', 10, 2)->nullable()->after('is_friendly');
            $table->decimal('away_team_fee', 10, 2)->nullable()->after('home_team_fee');
            $table->decimal('referee_fee', 10, 2)->nullable()->after('away_team_fee');
            $table->text('friendly_notes')->nullable()->after('referee_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_matches', function (Blueprint $table) {
            $table->dropColumn([
                'is_friendly',
                'home_team_fee',
                'away_team_fee',
                'referee_fee',
                'friendly_notes'
            ]);
        });
    }
};

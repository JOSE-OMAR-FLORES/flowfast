<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            // Verificar y agregar columnas faltantes
            if (!Schema::hasColumn('players', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('players', 'league_id')) {
                $table->foreignId('league_id')->after('team_id')->constrained('leagues')->onDelete('cascade');
            }
            if (!Schema::hasColumn('players', 'email')) {
                $table->string('email')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('players', 'photo')) {
                $table->string('photo')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('players', 'status')) {
                $table->enum('status', ['active', 'injured', 'suspended', 'inactive'])->default('active')->after('position');
            }
            if (!Schema::hasColumn('players', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('players', 'matches_played')) {
                $table->integer('matches_played')->default(0)->after('notes');
            }
            if (!Schema::hasColumn('players', 'goals')) {
                $table->integer('goals')->default(0)->after('matches_played');
            }
            if (!Schema::hasColumn('players', 'assists')) {
                $table->integer('assists')->default(0)->after('goals');
            }
            if (!Schema::hasColumn('players', 'yellow_cards')) {
                $table->integer('yellow_cards')->default(0)->after('assists');
            }
            if (!Schema::hasColumn('players', 'red_cards')) {
                $table->integer('red_cards')->default(0)->after('yellow_cards');
            }
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'league_id',
                'email',
                'photo',
                'status',
                'notes',
                'matches_played',
                'goals',
                'assists',
                'yellow_cards',
                'red_cards'
            ]);
        });
    }
};

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
            if (!Schema::hasColumn('leagues', 'match_fee')) {
                $table->decimal('match_fee', 10, 2)->default(0.00)->after('registration_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            if (Schema::hasColumn('leagues', 'match_fee')) {
                $table->dropColumn('match_fee');
            }
        });
    }
};

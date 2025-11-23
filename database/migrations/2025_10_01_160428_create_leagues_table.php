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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->foreignId('sport_id')->constrained('sports');
            $table->foreignId('admin_id')->constrained('admins');
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('registration_fee', 10, 2)->default(0.00);
            $table->decimal('match_fee_per_team', 10, 2)->default(0.00);
            $table->decimal('penalty_fee', 10, 2)->default(0.00);
            $table->decimal('referee_payment', 10, 2)->default(0.00);
            $table->enum('status', ['draft', 'active', 'inactive', 'archived'])->default('draft');
            $table->timestamps();
            
            $table->foreign('manager_id')->references('id')->on('league_managers');
            $table->index('sport_id');
            $table->index('admin_id');
            $table->index('manager_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leagues');
    }
};

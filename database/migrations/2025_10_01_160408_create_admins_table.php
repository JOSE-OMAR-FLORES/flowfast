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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('company_name', 191)->nullable();
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->enum('subscription_status', ['active', 'suspended', 'cancelled'])->default('active');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->string('brand_logo', 191)->nullable();
            $table->json('brand_colors')->nullable();
            $table->timestamps();
            
            $table->index('subscription_status');
            $table->index('subscription_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};

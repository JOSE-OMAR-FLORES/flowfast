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
        // Agregar campos de Stripe a la tabla incomes
        Schema::table('incomes', function (Blueprint $table) {
            $table->string('stripe_payment_intent_id')->nullable()->after('payment_reference');
            $table->string('stripe_charge_id')->nullable()->after('stripe_payment_intent_id');
            $table->string('stripe_customer_id')->nullable()->after('stripe_charge_id');
            $table->json('stripe_metadata')->nullable()->after('stripe_customer_id');
        });
        
        // Agregar campos de Stripe a la tabla expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('stripe_payment_intent_id')->nullable()->after('payment_reference');
            $table->string('stripe_charge_id')->nullable()->after('stripe_payment_intent_id');
            $table->string('stripe_customer_id')->nullable()->after('stripe_charge_id');
            $table->json('stripe_metadata')->nullable()->after('stripe_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_payment_intent_id',
                'stripe_charge_id',
                'stripe_customer_id',
                'stripe_metadata'
            ]);
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_payment_intent_id',
                'stripe_charge_id',
                'stripe_customer_id',
                'stripe_metadata'
            ]);
        });
    }
};

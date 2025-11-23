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
        // MySQL no permite modificar directamente un ENUM, hay que recrear la columna
        DB::statement("ALTER TABLE expenses MODIFY COLUMN expense_type ENUM(
            'referee_payment',
            'referee_bonus',
            'referee_travel',
            'venue_rental',
            'equipment',
            'maintenance',
            'utilities',
            'staff_salary',
            'marketing',
            'insurance',
            'other'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a los valores originales
        DB::statement("ALTER TABLE expenses MODIFY COLUMN expense_type ENUM(
            'referee_payment',
            'venue_rental',
            'equipment',
            'maintenance',
            'utilities',
            'staff_salary',
            'marketing',
            'insurance',
            'other'
        )");
    }
};

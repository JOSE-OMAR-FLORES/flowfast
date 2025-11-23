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
        $driver = config('database.default');
        
        if ($driver === 'pgsql') {
            // PostgreSQL: Modificar tipo string y agregar/actualizar constraint
            Schema::table('expenses', function (Blueprint $table) {
                $table->string('expense_type', 50)->change();
            });
            
            // Eliminar constraint anterior si existe y crear uno nuevo
            DB::statement("ALTER TABLE expenses DROP CONSTRAINT IF EXISTS expenses_expense_type_check");
            DB::statement("ALTER TABLE expenses ADD CONSTRAINT expenses_expense_type_check CHECK (expense_type IN (
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
            ))");
        } else {
            // MySQL: Modificar ENUM directamente
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = config('database.default');
        
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE expenses DROP CONSTRAINT IF EXISTS expenses_expense_type_check");
            DB::statement("ALTER TABLE expenses ADD CONSTRAINT expenses_expense_type_check CHECK (expense_type IN (
                'referee_payment',
                'venue_rental',
                'equipment',
                'maintenance',
                'utilities',
                'staff_salary',
                'marketing',
                'insurance',
                'other'
            ))");
        } else {
            // Revertir a los valores originales en MySQL
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
    }
};

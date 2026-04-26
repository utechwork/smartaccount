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
        Schema::table('account_statements', function (Blueprint $table) {
            // Check if value column exists and drop it
            if (Schema::hasColumn('account_statements', 'value')) {
                $table->dropColumn('value');
            }
            
            // Add value_date column if it doesn't exist
            if (!Schema::hasColumn('account_statements', 'value_date')) {
                $table->date('value_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_statements', function (Blueprint $table) {
            if (Schema::hasColumn('account_statements', 'value_date')) {
                $table->dropColumn('value_date');
            }
            
            if (!Schema::hasColumn('account_statements', 'value')) {
                $table->decimal('value', 12, 2)->nullable();
            }
        });
    }
};

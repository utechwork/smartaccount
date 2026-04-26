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
            $table->text('expense_details')->nullable()->after('vendor_id')->comment('Details about the expense');
            $table->text('remark')->nullable()->after('expense_details')->comment('Additional remarks or notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_statements', function (Blueprint $table) {
            $table->dropColumn(['expense_details', 'remark']);
        });
    }
};

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
        Schema::create('petty_cashes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('availability')->nullable(); // Yes/No
            $table->string('expense_type')->nullable(); // Maintenance, Miscellaneous
            $table->decimal('expense_paid', 12, 2)->default(0);
            $table->string('payment_method')->default('Cash'); // Cash, Bank Transfer, etc.
            $table->string('cleared_by')->nullable(); // HDFC Bank Ltd, etc.
            $table->string('vendor_name')->nullable();
            $table->text('expense_details')->nullable();
            $table->text('remark')->nullable();
            $table->enum('expense_category', ['Maintenance', 'Miscellaneous'])->nullable();
            $table->boolean('is_withdrawal')->default(false); // For withdrawals from bank
            $table->timestamps();
            $table->index('date');
            $table->index('expense_type');
            $table->index('expense_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cashes');
    }
};

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
        Schema::create('account_statements', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->text('narration')->nullable();
            $table->string('chq_ref_no')->nullable();
            $table->date('value_date')->nullable();
            $table->decimal('withdrawal_amt', 12, 2)->nullable();
            $table->decimal('deposit_amt', 12, 2)->nullable();
            $table->decimal('closing_balance', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_statements');
    }
};

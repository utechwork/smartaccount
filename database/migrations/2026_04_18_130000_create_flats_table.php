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
        Schema::create('flats', function (Blueprint $table) {
            $table->id();
            $table->string('flat_number')->unique();
            $table->integer('floor_number');
            $table->string('owner_name')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('owner_phone')->nullable();
            $table->decimal('maintenance_amount', 10, 2)->nullable();
            $table->string('maintenance_status')->default('pending'); // pending, paid, overdue
            $table->date('last_maintenance_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('floor_number');
            $table->index('maintenance_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flats');
    }
};

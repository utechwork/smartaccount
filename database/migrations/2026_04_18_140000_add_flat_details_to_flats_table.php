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
        Schema::table('flats', function (Blueprint $table) {
            // Add new columns
            $table->string('flat_type')->default('2BHK')->after('floor_number'); // 1BHK or 2BHK
            $table->string('occupancy_type')->default('owner')->after('flat_type'); // owner or tenant
            $table->boolean('builder_paid_exception')->default(false)->after('notes'); // Paid to builder exception
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flats', function (Blueprint $table) {
            $table->dropColumn(['flat_type', 'occupancy_type', 'builder_paid_exception']);
        });
    }
};

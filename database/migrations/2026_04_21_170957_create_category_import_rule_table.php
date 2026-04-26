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
        Schema::create('category_import_rule', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_rule_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();
            
            $table->foreign('import_rule_id')->references('id')->on('import_rules')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unique(['import_rule_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_import_rule');
    }
};

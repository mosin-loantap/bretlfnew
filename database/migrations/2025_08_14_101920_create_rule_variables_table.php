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
        Schema::create('rule_variables', function (Blueprint $table) {
            $table->string('variable_id', 64)->collation('utf8mb4_unicode_ci')->primary();
            $table->uuid('partner_id');
            $table->string('variable_name');
            $table->string('description');
            $table->string('data_type');
            $table->string('source');
            $table->timestamps();
        });

        // Add foreign key constraint for rule_conditions after rule_variables table is created
        Schema::table('rule_conditions', function (Blueprint $table) {
            $table->foreign('variable_id')->references('variable_id')->on('rule_variables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rule_conditions', function (Blueprint $table) {
            $table->dropForeign(['variable_id']);
        });
        
        Schema::dropIfExists('rule_variables');
    }
};

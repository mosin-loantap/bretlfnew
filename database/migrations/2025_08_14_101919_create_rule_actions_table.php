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
        Schema::create('rule_actions', function (Blueprint $table) {
            $table->id('action_id');
            $table->unsignedBigInteger('rule_id');
            $table->unsignedBigInteger('partner_id');
            $table->string('action_type'); // approve, reject, refer, set_interest_rate
            $table->string('parameters')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('rule_id')->references('rule_id')->on('rules')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_actions');
    }
};

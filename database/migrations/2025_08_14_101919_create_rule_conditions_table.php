<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rule_conditions', function (Blueprint $table) {
            $table->id('condition_id');
            $table->unsignedBigInteger('rule_id');
            $table->string('variable_name');
            $table->string('operator');
            $table->string('value');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('rule_id')->references('rule_id')->on('rules')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_conditions');
    }
};

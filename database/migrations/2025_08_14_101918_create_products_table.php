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
        Schema::create('products', function (Blueprint $table) {
           $table->id('product_id');
            $table->unsignedBigInteger('partner_id'); // Foreign key to partners table
            $table->string('product_name');
            $table->string('product_type'); // personal loan, business loan
            $table->string('product_category'); // secured, unsecured
            $table->decimal('min_amount', 18, 2);
            $table->decimal('max_amount', 18, 2);
            $table->integer('min_tenure');
            $table->integer('max_tenure');
            $table->decimal('interest_rate', 5, 2);
            $table->decimal('processing_fee', 5, 2)->nullable();
            $table->decimal('prepayment_penalty', 5, 2)->nullable();
            $table->boolean('active_flag')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
             $table->foreign('partner_id')->references('partner_id')->on('partners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

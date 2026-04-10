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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Primary Info
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('password')->nullable();

            // Other Details
            $table->string('payment_terms')->nullable();
            $table->string('gst_number')->nullable();
            $table->decimal('discount', 5, 2)->default(0);
            $table->string('gst_treatment')->nullable();
            $table->string('place_of_supply')->nullable();
            $table->string('pan_number')->nullable();
            $table->decimal('credit_limit', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

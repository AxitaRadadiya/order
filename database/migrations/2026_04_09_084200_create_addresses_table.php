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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
 
            // Billing Address
            $table->string('billing_attention')->nullable();
            $table->string('billing_street')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_pin_code', 10)->nullable();
            $table->string('billing_country')->nullable()->default('India');
            $table->string('billing_gst_number', 20)->nullable();
 
            // Shipping Address
            $table->boolean('same_as')->default(false);
            $table->string('shipping_attention')->nullable();
            $table->string('shipping_street')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_pin_code', 10)->nullable();
            $table->string('shipping_country')->nullable()->default('India');
            $table->string('shipping_gst_number', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};


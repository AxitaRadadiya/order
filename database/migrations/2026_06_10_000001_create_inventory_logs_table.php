<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('item_variant_id');
            $table->unsignedBigInteger('order_master_id')->nullable();

            $table->enum('type', ['deduct', 'restock']);
            $table->unsignedInteger('qty');

            $table->string('note')->nullable();
            $table->unsignedBigInteger('created_by');

            $table->timestamps();

            $table->foreign('item_variant_id')
                ->references('id')
                ->on('item_variants')
                ->onDelete('cascade');

            $table->foreign('order_master_id')
                ->references('id')
                ->on('order_masters');

            $table->foreign('created_by')
                ->references('id')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};


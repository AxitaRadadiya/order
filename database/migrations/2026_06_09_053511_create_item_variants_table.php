<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            $table->foreignId('color_id')
                ->constrained('colors')
                ->cascadeOnDelete();

            $table->foreignId('size_id')
                ->constrained('sizes')
                ->cascadeOnDelete();

            $table->integer('quantity')->default(0);

            $table->timestamps();

            $table->unique(['item_id', 'color_id', 'size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_variants');
    }
};

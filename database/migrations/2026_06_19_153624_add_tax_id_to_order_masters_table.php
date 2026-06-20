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
        Schema::table('order_masters', function (Blueprint $table) {
            // Add tax_id foreign key
            $table->foreignId('tax_id')
                  ->nullable()
                  ->after('adjustment')
                  ->constrained('tax_masters')
                  ->nullOnDelete();
            
            // Add tax_percentage for historical accuracy (in case tax master is deleted later)
            $table->decimal('tax_percentage', 5, 2)
                  ->nullable()
                  ->after('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_masters', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['tax_id']);
            
            // Drop columns
            $table->dropColumn(['tax_id', 'tax_percentage']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('item_variants', function (Blueprint $table) {
            $table->integer('production_quantity')->default(0)->after('quantity');
            $table->integer('sold_quantity')->default(0)->after('production_quantity');
        });
    }

   
    public function down(): void
    {
        Schema::table('item_variants', function (Blueprint $table) {
            $table->dropColumn(['production_quantity', 'sold_quantity']);
        });
    }
};
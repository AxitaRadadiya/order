<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Only add if it doesn't already exist
            if (!Schema::hasColumn('items', 'images')) {
                $table->json('images')->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'images')) {
                $table->dropColumn('images');
            }
        });
    }
};
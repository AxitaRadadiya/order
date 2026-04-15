<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'sub_category')) {
                $table->string('sub_category')->nullable()->after('category_id');
            }
            if (! Schema::hasColumn('items', 'sub_group')) {
                $table->string('sub_group')->nullable()->after('group_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'sub_category')) {
                $table->dropColumn('sub_category');
            }
            if (Schema::hasColumn('items', 'sub_group')) {
                $table->dropColumn('sub_group');
            }
        });
    }
};

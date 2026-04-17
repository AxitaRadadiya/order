<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('item_code')->nullable();
            $table->string('color')->nullable();
            $table->boolean('show_item_on_web')->default(true);
            $table->json('sizes')->nullable();
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['item_code', 'color', 'show_item_on_web', 'sizes']);
        });
    }
};

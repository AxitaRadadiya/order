<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('order_masters', function (Blueprint $table) {
            $table->decimal('markdown', 5, 2)->default(0)->after('discount');
        });
    }

    public function down()
    {
        Schema::table('order_masters', function (Blueprint $table) {
            $table->dropColumn('markdown');
        });
    }
};
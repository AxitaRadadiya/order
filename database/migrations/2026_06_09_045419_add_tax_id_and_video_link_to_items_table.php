<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('tax_id')
                ->nullable()
                ->after('id')
                ->constrained('tax_masters')
                ->nullOnDelete();

            $table->string('video_link')->nullable()->after('tax_id');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['tax_id']);
            $table->dropColumn(['tax_id', 'video_link']);
        });
    }
};

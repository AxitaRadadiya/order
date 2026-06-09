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
        Schema::table('users', function (Blueprint $table) {
            $table->string('shop_image')->nullable()->after('profile_image');
            $table->string('pan_card_image')->nullable()->after('shop_image');
            $table->string('gst_certificate_image')->nullable()->after('pan_card_image');
            $table->string('google_location_link')->nullable()->after('gst_certificate_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['shop_image', 'pan_card_image', 'gst_certificate_image', 'google_location_link']);
        });
    }
};

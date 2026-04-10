<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image')
                ->default('admin/dist/img/logo1.png')
                ->after('password');
        });

        DB::table('users')
            ->whereNull('profile_image')
            ->orWhere('profile_image', '')
            ->update(['profile_image' => 'admin/dist/img/logo1.png']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_image');
        });
    }
};

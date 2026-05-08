<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'distributor_verified')) {
                $table->boolean('distributor_verified')->default(false)->after('distributor_id');
            }
            if (! Schema::hasColumn('users', 'distributor_verified_at')) {
                $table->timestamp('distributor_verified_at')->nullable()->after('distributor_verified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'distributor_verified_at')) {
                $table->dropColumn('distributor_verified_at');
            }
            if (Schema::hasColumn('users', 'distributor_verified')) {
                $table->dropColumn('distributor_verified');
            }
        });
    }
};

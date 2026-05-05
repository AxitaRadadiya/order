<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('order_masters', function (Blueprint $table) {
            if (!Schema::hasColumn('order_masters', 'distributor_id')) {
                $table->foreignId('distributor_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('order_masters', 'distributor_approved')) {
                $table->boolean('distributor_approved')->default(false)->after('status');
            }
            if (!Schema::hasColumn('order_masters', 'distributor_approved_at')) {
                $table->timestamp('distributor_approved_at')->nullable()->after('distributor_approved');
            }
            if (!Schema::hasColumn('order_masters', 'visible_to_superadmin')) {
                $table->boolean('visible_to_superadmin')->default(true)->after('distributor_approved_at');
            }
        });
    }

    public function down()
    {
        Schema::table('order_masters', function (Blueprint $table) {
            if (Schema::hasColumn('order_masters', 'visible_to_superadmin')) {
                $table->dropColumn('visible_to_superadmin');
            }
            if (Schema::hasColumn('order_masters', 'distributor_approved_at')) {
                $table->dropColumn('distributor_approved_at');
            }
            if (Schema::hasColumn('order_masters', 'distributor_approved')) {
                $table->dropColumn('distributor_approved');
            }
            if (Schema::hasColumn('order_masters', 'distributor_id')) {
                $table->dropConstrainedForeignId('distributor_id');
            }
        });
    }
};

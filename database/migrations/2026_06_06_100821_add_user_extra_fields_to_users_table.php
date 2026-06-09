<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');

            $table->string('shop_name')->nullable()->after('last_name');

            $table->unsignedBigInteger('state_id')->nullable()->after('shop_name');
            $table->unsignedBigInteger('city_id')->nullable()->after('state_id');
        });

        // Populate first_name and last_name from existing `name` column
        DB::table('users')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                if (empty($user->name)) {
                    continue;
                }

                $parts = preg_split('/\s+/', trim($user->name));
                $first = $parts[0] ?? null;
                $last = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null;

                DB::table('users')->where('id', $user->id)->update([
                    'first_name' => $first,
                    'last_name' => $last,
                ]);
            }
        });

        // Add foreign keys
        Schema::table('users', function (Blueprint $table) {

            $table->foreign('state_id')
                  ->references('id')
                  ->on('states')
                  ->onDelete('set null');

            $table->foreign('city_id')
                  ->references('id')
                  ->on('cities')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);

            $table->dropColumn(['first_name', 'last_name', 'shop_name', 'state_id', 'city_id']);
        });
    }
};

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CustomerTypeBackfillSeeder extends Seeder
{
    public function run(): void
    {
        // Backfill: set role_id to retailer for users without a role
        $retailerRole = \App\Models\Role::firstOrCreate(['name' => 'retailer']);

        User::whereNull('role_id')->update(['role_id' => $retailerRole->id]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'super-admin'],
            ['name' => 'admin'],
            ['name' => 'retailer'],
            ['name' => 'distributor'],
            ['name' => 'sales manager'],
            ['name' => 'sales executive'],
            ['name' => 'production'],
            ['name' => 'accounts'],
            // ['name' => 'user'],
            // ['name' => 'owner'],
            // ['name' => 'supervisor'],
        ];

        // Populate table
        foreach ($roles as $r) {
            Role::updateOrCreate(['name' => $r['name']], $r);
        }

        // Assign all permissions to super-admin
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $permissionIds = Permission::pluck('id')->toArray();
            $superAdminRole->permissions()->sync($permissionIds);
        }

        // Ensure retailer/distributor have catalog permission by default
        $catalogPerm = Permission::where('name', 'catalog')->first();
        if ($catalogPerm) {
            $retailer = Role::where('name', 'retailer')->first();
            $distributor = Role::where('name', 'distributor')->first();

            if ($retailer) {
                $retailer->permissions()->syncWithoutDetaching([$catalogPerm->id]);
            }

            if ($distributor) {
                $distributor->permissions()->syncWithoutDetaching([$catalogPerm->id]);
            }
        }
    }
}

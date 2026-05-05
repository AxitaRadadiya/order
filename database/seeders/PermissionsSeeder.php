<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'Customer' => [
                'Customer' => [
                    
                    ['name' => 'customer-create'],
                    ['name' => 'customer-view'],
                    ['name' => 'customer-edit'],
                    ['name' => 'customer-delete'],
                ],
            ],
            'Item' => [
                'Item' => [
                    ['name' => 'item-view'],
                    ['name' => 'item-create'],
                    ['name' => 'item-edit'],
                    ['name' => 'item-delete'],
                ],
            ],
            'Order' => [
                'Order' => [
                    ['name' => 'order-view'],
                    ['name' => 'order-create'],
                    ['name' => 'order-edit'],
                    ['name' => 'order-delete'],
                ],
            ],
            'Setting' => [
                'Setting' => [
                    ['name' => 'setting-view'],
                    ['name' => 'setting-create'],
                    ['name' => 'setting-edit'],
                    ['name' => 'setting-delete'],
                ],
            ],
            'Report' => [
                'Report' => [
                    ['name' => 'report-view'],
                    ['name' => 'report-create'],
                    ['name' => 'report-edit'],
                    ['name' => 'report-delete'],
                ],
            ],
            'Catalog' => [
                'Catalog' => [
                    ['name' => 'catalog'],
                ],
            ],
            'System' => [
                'System' => [
                    // catch-all permission to grant full access via login logic
                    ['name' => 'all-modules'],
                ],
            ],
        ];

        foreach ($permissions as $modules) {
            foreach ($modules as $perms) {
                foreach ($perms as $perm) {
                    Permission::updateOrCreate(
                        ['name' => $perm['name']],
                        ['name' => $perm['name']]
                    );
                }
            }
        }
    }
}

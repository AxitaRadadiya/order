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
            'Dashboard' => [
                'Dashboard' => [
                    ['name' => 'dashboard-view'],
                ],
            ],
            'Profile' => [
                'Profile' => [
                    ['name' => 'profile-edit'],
                ],
            ],
            'User' => [
                'User' => [
                    
                    ['name' => 'user-create'],
                    ['name' => 'user-view'],
                    ['name' => 'user-edit'],
                    ['name' => 'user-delete'],
                ],
                'Role' => [
                    ['name' => 'role-create'],
                    ['name' => 'role-view'],
                    ['name' => 'role-edit'],
                    ['name' => 'role-delete'],
                ],
                'Permission' => [
                    ['name' => 'permission-view'],
                    ['name' => 'permission-create'],
                    ['name' => 'permission-edit'],
                    ['name' => 'permission-delete'],
                ],
            ],
            'General-Setting' => [
                'Setting' => [
                    ['name' => 'setting-view'],
                    ['name' => 'setting-create'],
                    ['name' => 'setting-edit'],
                    ['name' => 'setting-delete'],
                ],
                'Email' => [
                    ['name' => 'email-view'],
                    ['name' => 'email-create'],
                    ['name' => 'email-edit'],
                    ['name' => 'email-delete'],
                ],
            ],
            'Item' => [
                'Item' => [
                    ['name' => 'item-view'],
                    ['name' => 'item-create'],
                    ['name' => 'item-edit'],
                    ['name' => 'item-delete'],
                ],
                'Group' => [
                    ['name' => 'group-view'],
                    ['name' => 'group-create'],
                    ['name' => 'group-edit'],
                    ['name' => 'group-delete'],
                ],
                'Category' => [
                    ['name' => 'category-view'],
                    ['name' => 'category-create'],
                    ['name' => 'category-edit'],
                    ['name' => 'category-delete'],
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

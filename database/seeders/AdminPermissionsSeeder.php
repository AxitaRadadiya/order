<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userData = [
            'name' => 'Innoveza',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'role_id' => 1,
            'password' => Hash::make('12345678'),
            'status' => 1,
            'customer_type_id' => 1,
        ];

        $user = User::updateOrCreate(
            ['email' => $userData['email']], // Unique identifier
            $userData
        );

        // Attach role_id for super-admin if exists
        $role = Role::where('name', 'super-admin')->first();
        if ($role) {
            $user->role()->associate($role);
            $user->save();
        }
    }
}

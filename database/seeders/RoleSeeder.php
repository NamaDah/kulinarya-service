<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full access to all features and settings',
                'is_active' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrative access to manage the system',
                'is_active' => true,
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Staff members who manage orders and products',
                'is_active' => true,
            ],
            [
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Regular customers who can place orders',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}

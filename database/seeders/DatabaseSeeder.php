<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call RBAC seeders first
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            MenuItemSeeder::class,
        ]);

        // Create super admin user
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
        ]);
        $superAdmin->assignRoles(['super-admin']);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRoles(['admin']);

        // Create staff user
        $staff = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
        ]);
        $staff->assignRoles(['staff']);

        // Create test customer user
        $customer = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);
        $customer->assignRoles(['customer']);

        // Create additional customers
        User::factory(5)->create()->each(function ($user) {
            $user->assignRoles(['customer']);
        });

        // Call other seeders
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
        ]);
    }
}

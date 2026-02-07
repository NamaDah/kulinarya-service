<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Users module
            ['name' => 'View Users', 'slug' => 'users-view', 'module' => 'users', 'description' => 'View user list and details'],
            ['name' => 'Create Users', 'slug' => 'users-create', 'module' => 'users', 'description' => 'Create new users'],
            ['name' => 'Edit Users', 'slug' => 'users-edit', 'module' => 'users', 'description' => 'Edit existing users'],
            ['name' => 'Delete Users', 'slug' => 'users-delete', 'module' => 'users', 'description' => 'Delete users'],

            // Roles module
            ['name' => 'View Roles', 'slug' => 'roles-view', 'module' => 'roles', 'description' => 'View roles list and details'],
            ['name' => 'Create Roles', 'slug' => 'roles-create', 'module' => 'roles', 'description' => 'Create new roles'],
            ['name' => 'Edit Roles', 'slug' => 'roles-edit', 'module' => 'roles', 'description' => 'Edit existing roles'],
            ['name' => 'Delete Roles', 'slug' => 'roles-delete', 'module' => 'roles', 'description' => 'Delete roles'],

            // Products module
            ['name' => 'View Products', 'slug' => 'products-view', 'module' => 'products', 'description' => 'View product list and details'],
            ['name' => 'Create Products', 'slug' => 'products-create', 'module' => 'products', 'description' => 'Create new products'],
            ['name' => 'Edit Products', 'slug' => 'products-edit', 'module' => 'products', 'description' => 'Edit existing products'],
            ['name' => 'Delete Products', 'slug' => 'products-delete', 'module' => 'products', 'description' => 'Delete products'],

            // Categories module
            ['name' => 'View Categories', 'slug' => 'categories-view', 'module' => 'categories', 'description' => 'View category list and details'],
            ['name' => 'Create Categories', 'slug' => 'categories-create', 'module' => 'categories', 'description' => 'Create new categories'],
            ['name' => 'Edit Categories', 'slug' => 'categories-edit', 'module' => 'categories', 'description' => 'Edit existing categories'],
            ['name' => 'Delete Categories', 'slug' => 'categories-delete', 'module' => 'categories', 'description' => 'Delete categories'],

            // Orders module
            ['name' => 'View Orders', 'slug' => 'orders-view', 'module' => 'orders', 'description' => 'View order list and details'],
            ['name' => 'Create Orders', 'slug' => 'orders-create', 'module' => 'orders', 'description' => 'Create new orders'],
            ['name' => 'Edit Orders', 'slug' => 'orders-edit', 'module' => 'orders', 'description' => 'Edit/update order status'],
            ['name' => 'Delete Orders', 'slug' => 'orders-delete', 'module' => 'orders', 'description' => 'Delete orders'],
            ['name' => 'View All Orders', 'slug' => 'orders-view-all', 'module' => 'orders', 'description' => 'View all orders (not just own)'],

            // Coupons module
            ['name' => 'View Coupons', 'slug' => 'coupons-view', 'module' => 'coupons', 'description' => 'View coupon list and details'],
            ['name' => 'Create Coupons', 'slug' => 'coupons-create', 'module' => 'coupons', 'description' => 'Create new coupons'],
            ['name' => 'Edit Coupons', 'slug' => 'coupons-edit', 'module' => 'coupons', 'description' => 'Edit existing coupons'],
            ['name' => 'Delete Coupons', 'slug' => 'coupons-delete', 'module' => 'coupons', 'description' => 'Delete coupons'],

            // Ratings module
            ['name' => 'View Ratings', 'slug' => 'ratings-view', 'module' => 'ratings', 'description' => 'View rating list and details'],
            ['name' => 'Delete Ratings', 'slug' => 'ratings-delete', 'module' => 'ratings', 'description' => 'Delete ratings'],

            // Reports module
            ['name' => 'View Reports', 'slug' => 'reports-view', 'module' => 'reports', 'description' => 'View reports and analytics'],
            ['name' => 'Export Reports', 'slug' => 'reports-export', 'module' => 'reports', 'description' => 'Export report data'],

            // Settings module
            ['name' => 'View Settings', 'slug' => 'settings-view', 'module' => 'settings', 'description' => 'View system settings'],
            ['name' => 'Edit Settings', 'slug' => 'settings-edit', 'module' => 'settings', 'description' => 'Edit system settings'],

            // Menu module
            ['name' => 'View Menus', 'slug' => 'menus-view', 'module' => 'menus', 'description' => 'View menu items'],
            ['name' => 'Manage Menus', 'slug' => 'menus-manage', 'module' => 'menus', 'description' => 'Create, edit, delete menu items'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }

        // Assign all permissions to Admin role
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $allPermissions = Permission::whereNotIn('module', ['settings'])->pluck('id');
            $adminRole->permissions()->sync($allPermissions);
        }

        // Assign staff permissions
        $staffRole = Role::where('slug', 'staff')->first();
        if ($staffRole) {
            $staffPermissions = Permission::whereIn('slug', [
                'products-view',
                'products-edit',
                'categories-view',
                'orders-view',
                'orders-view-all',
                'orders-edit',
                'coupons-view',
                'ratings-view',
            ])->pluck('id');
            $staffRole->permissions()->sync($staffPermissions);
        }

        // Assign customer permissions
        $customerRole = Role::where('slug', 'customer')->first();
        if ($customerRole) {
            $customerPermissions = Permission::whereIn('slug', [
                'products-view',
                'categories-view',
                'orders-view',
                'orders-create',
                'coupons-view',
                'ratings-view',
            ])->pluck('id');
            $customerRole->permissions()->sync($customerPermissions);
        }
    }
}

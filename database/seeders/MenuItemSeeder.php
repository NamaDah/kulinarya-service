<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Role;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main navigation items
        $menus = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'dashboard',
                'route' => 'dashboard',
                'order' => 1,
            ],
            [
                'name' => 'Orders',
                'slug' => 'orders',
                'icon' => 'shopping-cart',
                'route' => 'orders.index',
                'order' => 2,
                'children' => [
                    ['name' => 'All Orders', 'slug' => 'orders-all', 'route' => 'orders.index', 'order' => 1],
                    ['name' => 'Pending Orders', 'slug' => 'orders-pending', 'route' => 'orders.pending', 'order' => 2],
                    ['name' => 'Completed Orders', 'slug' => 'orders-completed', 'route' => 'orders.completed', 'order' => 3],
                ],
            ],
            [
                'name' => 'Products',
                'slug' => 'products',
                'icon' => 'box',
                'route' => 'products.index',
                'order' => 3,
                'children' => [
                    ['name' => 'All Products', 'slug' => 'products-all', 'route' => 'products.index', 'order' => 1],
                    ['name' => 'Add Product', 'slug' => 'products-add', 'route' => 'products.create', 'order' => 2],
                    ['name' => 'Categories', 'slug' => 'categories', 'route' => 'categories.index', 'order' => 3],
                ],
            ],
            [
                'name' => 'Customers',
                'slug' => 'customers',
                'icon' => 'users',
                'route' => 'customers.index',
                'order' => 4,
            ],
            [
                'name' => 'Coupons',
                'slug' => 'coupons',
                'icon' => 'tag',
                'route' => 'coupons.index',
                'order' => 5,
            ],
            [
                'name' => 'Ratings',
                'slug' => 'ratings',
                'icon' => 'star',
                'route' => 'ratings.index',
                'order' => 6,
            ],
            [
                'name' => 'Reports',
                'slug' => 'reports',
                'icon' => 'bar-chart',
                'route' => 'reports.index',
                'order' => 7,
                'children' => [
                    ['name' => 'Sales Report', 'slug' => 'reports-sales', 'route' => 'reports.sales', 'order' => 1],
                    ['name' => 'Product Report', 'slug' => 'reports-products', 'route' => 'reports.products', 'order' => 2],
                    ['name' => 'Customer Report', 'slug' => 'reports-customers', 'route' => 'reports.customers', 'order' => 3],
                ],
            ],
            [
                'name' => 'Settings',
                'slug' => 'settings',
                'icon' => 'settings',
                'route' => 'settings.index',
                'order' => 8,
                'children' => [
                    ['name' => 'Roles & Permissions', 'slug' => 'settings-roles', 'route' => 'roles.index', 'order' => 1],
                    ['name' => 'Menu Management', 'slug' => 'settings-menus', 'route' => 'menus.index', 'order' => 2],
                    ['name' => 'General Settings', 'slug' => 'settings-general', 'route' => 'settings.general', 'order' => 3],
                ],
            ],
        ];

        foreach ($menus as $menuData) {
            $children = $menuData['children'] ?? [];
            unset($menuData['children']);

            $menu = MenuItem::firstOrCreate(['slug' => $menuData['slug']], $menuData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $menu->id;
                MenuItem::firstOrCreate(['slug' => $childData['slug']], $childData);
            }
        }

        // Assign menu items to roles
        $this->assignMenusToRoles();
    }

    private function assignMenusToRoles(): void
    {
        // Admin gets all menus
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $allMenus = MenuItem::pluck('id');
            $adminRole->menuItems()->sync($allMenus);
        }

        // Staff gets limited menus
        $staffRole = Role::where('slug', 'staff')->first();
        if ($staffRole) {
            $staffMenus = MenuItem::whereIn('slug', [
                'dashboard',
                'orders', 'orders-all', 'orders-pending', 'orders-completed',
                'products', 'products-all', 'categories',
                'coupons',
                'ratings',
            ])->pluck('id');
            $staffRole->menuItems()->sync($staffMenus);
        }

        // Customer gets minimal menus
        $customerRole = Role::where('slug', 'customer')->first();
        if ($customerRole) {
            $customerMenus = MenuItem::whereIn('slug', [
                'dashboard',
                'orders', 'orders-all',
            ])->pluck('id');
            $customerRole->menuItems()->sync($customerMenus);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get users with this role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role')
            ->withTimestamps();
    }

    /**
     * Get permissions for this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
            ->withTimestamps();
    }

    /**
     * Get menu items accessible by this role.
     */
    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'role_menu_item')
            ->withTimestamps();
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Check if role has access to a menu item.
     */
    public function hasMenuAccess(string $menuSlug): bool
    {
        return $this->menuItems()->where('slug', $menuSlug)->exists();
    }

    /**
     * Assign permissions to role.
     */
    public function givePermissions(array $permissionSlugs): void
    {
        $permissions = Permission::whereIn('slug', $permissionSlugs)->get();
        $this->permissions()->syncWithoutDetaching($permissions);
    }

    /**
     * Revoke permissions from role.
     */
    public function revokePermissions(array $permissionSlugs): void
    {
        $permissions = Permission::whereIn('slug', $permissionSlugs)->get();
        $this->permissions()->detach($permissions);
    }

    /**
     * Sync permissions for role.
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Sync menu items for role.
     */
    public function syncMenuItems(array $menuItemIds): void
    {
        $this->menuItems()->sync($menuItemIds);
    }
}

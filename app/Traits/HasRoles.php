<?php

namespace App\Traits;

use App\Models\MenuItem;
use App\Models\Permission;
use App\Models\Role;

trait HasRoles
{
    /**
     * Get roles for this user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role')
            ->withTimestamps();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->roles()->whereIn('slug', $roleSlugs)->exists();
    }

    /**
     * Check if user has all of the given roles.
     */
    public function hasAllRoles(array $roleSlugs): bool
    {
        return $this->roles()->whereIn('slug', $roleSlugs)->count() === count($roleSlugs);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug);
            })
            ->exists();
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionSlugs) {
                $query->whereIn('slug', $permissionSlugs);
            })
            ->exists();
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        $userPermissions = $this->getAllPermissions()->pluck('slug')->toArray();
        
        return count(array_intersect($permissionSlugs, $userPermissions)) === count($permissionSlugs);
    }

    /**
     * Get all permissions for this user through their roles.
     */
    public function getAllPermissions()
    {
        return Permission::whereHas('roles', function ($query) {
            $query->whereIn('roles.id', $this->roles()->pluck('roles.id'));
        })->get();
    }

    /**
     * Get accessible menu items for this user.
     */
    public function getAccessibleMenuItems()
    {
        $roleIds = $this->roles()->pluck('roles.id');

        return MenuItem::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('roles.id', $roleIds);
        })
        ->with(['children' => function ($query) use ($roleIds) {
            $query->whereHas('roles', function ($q) use ($roleIds) {
                $q->whereIn('roles.id', $roleIds);
            })->active()->orderBy('order');
        }])
        ->root()
        ->active()
        ->orderBy('order')
        ->get();
    }

    /**
     * Assign roles to user.
     */
    public function assignRoles(array $roleSlugs): void
    {
        $roles = Role::whereIn('slug', $roleSlugs)->where('is_active', true)->get();
        $this->roles()->syncWithoutDetaching($roles);
    }

    /**
     * Remove roles from user.
     */
    public function removeRoles(array $roleSlugs): void
    {
        $roles = Role::whereIn('slug', $roleSlugs)->get();
        $this->roles()->detach($roles);
    }

    /**
     * Sync roles for user.
     */
    public function syncRoles(array $roleIds): void
    {
        $this->roles()->sync($roleIds);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is super admin (has all permissions).
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }
}

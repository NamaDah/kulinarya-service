<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icon',
        'route',
        'url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get parent menu item.
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Get child menu items.
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->orderBy('order');
    }

    /**
     * Get roles that can access this menu item.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menu_item')
            ->withTimestamps();
    }

    /**
     * Scope to get only active menu items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only root menu items (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get menu tree structure.
     */
    public static function getMenuTree()
    {
        return static::with('children')
            ->root()
            ->active()
            ->orderBy('order')
            ->get();
    }

    /**
     * Get menu items accessible by a specific role.
     */
    public static function getMenuForRole(Role $role)
    {
        $menuItemIds = $role->menuItems()->pluck('menu_items.id');

        return static::with(['children' => function ($query) use ($menuItemIds) {
            $query->whereIn('id', $menuItemIds)
                  ->active()
                  ->orderBy('order');
        }])
        ->whereIn('id', $menuItemIds)
        ->root()
        ->active()
        ->orderBy('order')
        ->get();
    }
}

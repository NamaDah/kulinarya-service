<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuItemResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\MenuItem;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Role::query()->withCount('users');

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->boolean('with_permissions')) {
            $query->with('permissions');
        }

        if ($request->boolean('with_menu_items')) {
            $query->with('menuItems');
        }

        $roles = $query->latest()->paginate(15);

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RoleResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'menu_items' => 'nullable|array',
            'menu_items.*' => 'exists:menu_items,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        if (!empty($validated['menu_items'])) {
            $role->menuItems()->sync($validated['menu_items']);
        }

        return new RoleResource($role->load(['permissions', 'menuItems']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): RoleResource
    {
        return new RoleResource($role->load(['permissions', 'menuItems'])->loadCount('users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): RoleResource
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'sometimes|required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'menu_items' => 'nullable|array',
            'menu_items.*' => 'exists:menu_items,id',
        ]);

        $role->update([
            'name' => $validated['name'] ?? $role->name,
            'slug' => $validated['slug'] ?? $role->slug,
            'description' => $validated['description'] ?? $role->description,
            'is_active' => $validated['is_active'] ?? $role->is_active,
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        if (isset($validated['menu_items'])) {
            $role->menuItems()->sync($validated['menu_items']);
        }

        return new RoleResource($role->load(['permissions', 'menuItems']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): Response
    {
        // Prevent deleting super-admin role
        if ($role->slug === 'super-admin') {
            abort(403, 'Cannot delete the super-admin role.');
        }

        $role->delete();

        return response()->noContent();
    }

    /**
     * List all permissions grouped by module.
     */
    public function permissions(): AnonymousResourceCollection
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();

        return PermissionResource::collection($permissions);
    }

    /**
     * Sync permissions for a role.
     */
    public function syncPermissions(Request $request, Role $role): RoleResource
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions']);

        return new RoleResource($role->load('permissions'));
    }

    /**
     * Sync menu items for a role.
     */
    public function syncMenuItems(Request $request, Role $role): RoleResource
    {
        $validated = $request->validate([
            'menu_items' => 'required|array',
            'menu_items.*' => 'exists:menu_items,id',
        ]);

        $role->menuItems()->sync($validated['menu_items']);

        return new RoleResource($role->load('menuItems'));
    }
}

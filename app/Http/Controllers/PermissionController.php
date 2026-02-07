<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Permission::query();

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('module')) {
            $query->where('module', $request->module);
        }

        $permissions = $query->orderBy('module')->orderBy('name')->paginate(50);

        return PermissionResource::collection($permissions);
    }

    /**
     * Get permissions grouped by module.
     */
    public function grouped(): array
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();

        return $permissions->groupBy('module')->map(function ($items) {
            return PermissionResource::collection($items);
        })->toArray();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): PermissionResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:permissions,slug',
            'module' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['module'] . '-' . $validated['name']),
            'module' => $validated['module'],
            'description' => $validated['description'] ?? null,
        ]);

        return new PermissionResource($permission);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission): PermissionResource
    {
        return new PermissionResource($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission): PermissionResource
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:permissions,slug,' . $permission->id,
            'module' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $permission->update($validated);

        return new PermissionResource($permission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission): Response
    {
        $permission->delete();

        return response()->noContent();
    }

    /**
     * Get list of unique modules.
     */
    public function modules(): array
    {
        return Permission::distinct()->pluck('module')->toArray();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuItemResource;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MenuItem::query();

        if ($request->boolean('tree')) {
            // Return hierarchical tree structure
            $menus = MenuItem::with('children')
                ->root()
                ->orderBy('order')
                ->get();

            return MenuItemResource::collection($menus);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->boolean('active_only')) {
            $query->active();
        }

        $menus = $query->orderBy('order')->paginate(50);

        return MenuItemResource::collection($menus);
    }

    /**
     * Get menu tree for the authenticated user based on their roles.
     */
    public function userMenu(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            // Super admin sees all menus
            $menus = MenuItem::with('children')
                ->root()
                ->active()
                ->orderBy('order')
                ->get();
        } else {
            $menus = $user->getAccessibleMenuItems();
        }

        return MenuItemResource::collection($menus);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): MenuItemResource
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:menu_items,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:menu_items,slug',
            'icon' => 'nullable|string|max:100',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $menuItem = MenuItem::create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'icon' => $validated['icon'] ?? null,
            'route' => $validated['route'] ?? null,
            'url' => $validated['url'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return new MenuItemResource($menuItem->load('children'));
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menuItem): MenuItemResource
    {
        return new MenuItemResource($menuItem->load(['children', 'parent']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuItem $menuItem): MenuItemResource
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:menu_items,id',
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:menu_items,slug,' . $menuItem->id,
            'icon' => 'nullable|string|max:100',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Prevent setting parent to self or descendants
        if (isset($validated['parent_id']) && $validated['parent_id'] == $menuItem->id) {
            abort(422, 'A menu item cannot be its own parent.');
        }

        $menuItem->update($validated);

        return new MenuItemResource($menuItem->load(['children', 'parent']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem): Response
    {
        $menuItem->delete();

        return response()->noContent();
    }

    /**
     * Reorder menu items.
     */
    public function reorder(Request $request): Response
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.order' => 'required|integer|min:0',
            'items.*.parent_id' => 'nullable|exists:menu_items,id',
        ]);

        foreach ($validated['items'] as $item) {
            MenuItem::where('id', $item['id'])->update([
                'order' => $item['order'],
                'parent_id' => $item['parent_id'] ?? null,
            ]);
        }

        return response()->noContent();
    }
}

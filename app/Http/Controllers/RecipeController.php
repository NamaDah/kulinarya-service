<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of recipes.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Recipe::with('category');

        if ($request->has('cuisine_type')) {
            $query->where('cuisine_type', $request->cuisine_type);
        }

        return response()->json($query->latest()->get());
    }

    /**
     * Return featured recipes.
     */
    public function featured(): JsonResponse
    {
        $recipes = Recipe::with('category')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return response()->json($recipes);
    }

    /**
     * Display the specified recipe with ingredients.
     */
    public function show(string $slug): JsonResponse
    {
        $recipe = Recipe::with(['category', 'ingredients.product'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($recipe);
    }
}

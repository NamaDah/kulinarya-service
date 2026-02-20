<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): JsonResponse
    {
        return response()->json(Category::orderBy('name')->get());
    }

    /**
     * Display the specified category with its products.
     */
    public function show(string $slug): JsonResponse
    {
        $category = Category::with('products')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($category);
    }
}

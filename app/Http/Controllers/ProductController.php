<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category');

        if ($request->has('cuisine_type')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('cuisine_type', $request->cuisine_type);
            });
        }

        return response()->json($query->orderBy('name')->get());
    }

    /**
     * Display the specified product.
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($product);
    }
}

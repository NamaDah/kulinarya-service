<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Http\Requests\UpdateRatingRequest;
use App\Http\Resources\RatingResource;
use App\Models\Order;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Rating::query()->with(['user', 'order']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        $ratings = $query->latest()->paginate(15);

        return RatingResource::collection($ratings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRatingRequest $request): RatingResource
    {
        // Verify the order belongs to the user and is completed
        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== $request->user()->id) {
            abort(403, 'You can only rate your own orders.');
        }

        if ($order->status !== 'completed') {
            abort(422, 'You can only rate completed orders.');
        }

        $rating = Rating::create([
            'user_id' => $request->user()->id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return new RatingResource($rating->load(['user', 'order']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Rating $rating): RatingResource
    {
        return new RatingResource($rating->load(['user', 'order']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRatingRequest $request, Rating $rating): RatingResource
    {
        $this->authorize('update', $rating);

        $rating->update($request->validated());

        return new RatingResource($rating->load(['user', 'order']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating): Response
    {
        $this->authorize('delete', $rating);

        $rating->delete();

        return response()->noContent();
    }

    /**
     * Get ratings for a specific order.
     */
    public function forOrder(Order $order): RatingResource
    {
        $rating = $order->rating;

        if (!$rating) {
            abort(404, 'No rating found for this order.');
        }

        return new RatingResource($rating->load(['user', 'order']));
    }
}

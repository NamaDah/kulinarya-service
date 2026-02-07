<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderProductResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class OrderProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Order $order): AnonymousResourceCollection
    {
        $this->authorize('view', $order);

        $items = $order->orderProducts()->with('product.category')->get();

        return OrderProductResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Order $order): OrderProductResource
    {
        $this->authorize('update', $order);

        if ($order->status !== 'pending') {
            abort(422, 'Cannot add items to an order that is not pending.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->is_available) {
            abort(422, "Product {$product->name} is not available.");
        }

        $unitPrice = $product->price;
        $subtotal = $unitPrice * $validated['quantity'];

        $orderProduct = $order->orderProducts()->create([
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'special_instructions' => $validated['special_instructions'],
        ]);

        // Update order totals
        $this->recalculateOrderTotals($order);

        return new OrderProductResource($orderProduct->load('product'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order, OrderProduct $item): OrderProductResource
    {
        $this->authorize('view', $order);

        return new OrderProductResource($item->load('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order, OrderProduct $item): OrderProductResource
    {
        $this->authorize('update', $order);

        if ($order->status !== 'pending') {
            abort(422, 'Cannot update items of an order that is not pending.');
        }

        $validated = $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
            'special_instructions' => 'nullable|string',
        ]);

        if (isset($validated['quantity'])) {
            $validated['subtotal'] = $item->unit_price * $validated['quantity'];
        }

        $item->update($validated);

        // Update order totals
        $this->recalculateOrderTotals($order);

        return new OrderProductResource($item->load('product'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order, OrderProduct $item): Response
    {
        $this->authorize('update', $order);

        if ($order->status !== 'pending') {
            abort(422, 'Cannot remove items from an order that is not pending.');
        }

        $item->delete();

        // Update order totals
        $this->recalculateOrderTotals($order);

        return response()->noContent();
    }

    /**
     * Recalculate order totals after item changes.
     */
    private function recalculateOrderTotals(Order $order): void
    {
        $subtotal = $order->orderProducts()->sum('subtotal');
        $total = $subtotal - $order->discount_amount + $order->delivery_fee;

        $order->update([
            'subtotal' => $subtotal,
            'total' => $total,
        ]);
    }
}

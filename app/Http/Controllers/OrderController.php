<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Order::query()
            ->with(['user', 'address', 'coupon', 'orderProducts.product'])
            ->where('user_id', $request->user()->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(15);

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): OrderResource
    {
        return DB::transaction(function () use ($request) {
            $subtotal = 0;
            $items = [];

            // Calculate subtotal from items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if (!$product->is_available) {
                    abort(422, "Product {$product->name} is not available.");
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $itemSubtotal,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ];
            }

            // Calculate discount
            $discountAmount = 0;
            if ($request->coupon_id) {
                $coupon = Coupon::findOrFail($request->coupon_id);
                
                if ($coupon->discount_type === 'percentage') {
                    $discountAmount = $subtotal * ($coupon->discount_value / 100);
                    if ($coupon->max_discount_amount) {
                        $discountAmount = min($discountAmount, $coupon->max_discount_amount);
                    }
                } else {
                    $discountAmount = $coupon->discount_value;
                }

                // Increment used count
                $coupon->increment('used_count');
            }

            // Default delivery fee (could be calculated based on address)
            $deliveryFee = 50.00;
            $total = $subtotal - $discountAmount + $deliveryFee;

            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => $request->user()->id,
                'address_id' => $request->address_id,
                'coupon_id' => $request->coupon_id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($items as $item) {
                $order->orderProducts()->create($item);
            }

            return new OrderResource($order->load(['user', 'address', 'coupon', 'orderProducts.product']));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return new OrderResource($order->load(['user', 'address', 'coupon', 'orderProducts.product', 'rating']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): OrderResource
    {
        $this->authorize('update', $order);

        $data = $request->validated();

        // Set timestamps based on status
        if (isset($data['status'])) {
            if ($data['status'] === 'confirmed' && !$order->confirmed_at) {
                $data['confirmed_at'] = now();
            }
            if ($data['status'] === 'completed' && !$order->completed_at) {
                $data['completed_at'] = now();
            }
        }

        $order->update($data);

        return new OrderResource($order->load(['user', 'address', 'coupon', 'orderProducts.product']));
    }

    /**
     * Cancel the order.
     */
    public function cancel(Order $order): OrderResource
    {
        $this->authorize('update', $order);

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            abort(422, 'Order cannot be cancelled at this stage.');
        }

        $order->update([
            'status' => 'cancelled',
        ]);

        // Note: Refund logic would go here if payment was already made

        return new OrderResource($order->load(['user', 'address', 'coupon', 'orderProducts.product']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): Response
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->noContent();
    }
}

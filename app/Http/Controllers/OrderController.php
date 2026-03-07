<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Checkout: create an order from cart items and initiate payment.
     */
    public function checkout(CheckoutRequest $request, PaymentService $paymentService): JsonResponse
    {
        $user = $request->user();
        $items = $request->validated()['items'];
        // dd($request);
        $address = $request->address;

        // Fetch products and validate stock
        $productIds = collect($items)->pluck('product_id')->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            if (! $product || ! $product->in_stock) {
                return response()->json([
                    'message' => "Product \"{$product?->name}\" is out of stock.",
                ], 422);
            }
        }

        // Create order in a transaction
        $order = DB::transaction(function () use ($user, $items, $products, $address) {
            $totalAmount = 0;

            $order = Order::create([
                'user_id' => $user->id,
                'address' => $address,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'total_amount' => 0,
            ]);

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                $unitPrice = $product->price;
                $quantity = $item['quantity'];
                $totalAmount += $unitPrice * $quantity;

                $order->orderProducts()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            return $order;
        });

        // Create payment transaction
        $payment = $paymentService->createTransaction($order);

        $order->refresh();
        $order->load('orderProducts.product');

        return response()->json([
            'message' => 'Order created successfully.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'address' => $order->address,
                'payment_status' => $order->payment_status,
                'total_amount' => $order->total_amount,
                'snap_token' => $payment['snap_token'],
                'redirect_url' => $payment['redirect_url'],
                'items' => $order->orderProducts->map(fn ($op) => [
                    'id' => $op->id,
                    'product_id' => $op->product_id,
                    'product_name' => $op->product->name ?? '',
                    'quantity' => $op->quantity,
                    'unit_price' => $op->unit_price,
                ]),
                'created_at' => $order->created_at,
            ],
        ], 201);
    }

    /**
     * List authenticated user's orders.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with('orderProducts.product')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * Show a specific order detail (scoped to user).
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        if (! $order->belongsToUser($request->user()->id)) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $order->load('orderProducts.product');

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'address' => $order->address,
            'payment_status' => $order->payment_status,
            'payment_reference' => $order->payment_reference,
            'snap_token' => $order->snap_token,
            'total_amount' => $order->total_amount,
            'items' => $order->orderProducts->map(fn ($op) => [
                'id' => $op->id,
                'product_id' => $op->product_id,
                'product_name' => $op->product->name ?? '',
                'product_image' => $op->product->image_url ?? null,
                'quantity' => $op->quantity,
                'unit_price' => $op->unit_price,
            ]),
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
        ]);
    }
}

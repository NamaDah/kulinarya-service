<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminOrderController extends Controller
{
    /**
     * List all orders with optional status filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'orderProducts.product'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $orders = $query->paginate(15);

        return response()->json($orders);
    }

    /**
     * Show a specific order detail.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['user', 'orderProducts.product']);

        return response()->json([
            'id' => $order->id,
            'user' => $order->user ? [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
            ] : null,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_reference' => $order->payment_reference,
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

    /**
     * Manually update the status of an order.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,processing,confirmed,shipped,delivered,cancelled'],
        ]);

        $order->update([
            'status' => $request->input('status'),
        ]);

        try {
            return response()->json([
                'message' => 'Order status updated successfully.',
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update order status', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to update order status.',
            ], 500);
        }

        // return response()->json([
        //     'message' => 'Order status updated successfully.',
        //     'order' => [
        //         'id' => $order->id,
        //         'status' => $order->status,
        //         'payment_status' => $order->payment_status,
        //     ],
        // ]);
    }
}

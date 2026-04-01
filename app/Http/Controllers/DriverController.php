<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * List orders assigned to the authenticated driver.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->driverOrders()
            ->with('orderProducts.product', 'user')
            ->whereIn('status', [OrderStatus::Shipping])
            ->orderByDesc('updated_at')
            ->paginate(15);

        return response()->json($orders);
    }

    /**
     * List orders that need a driver (processing status, no driver assigned).
     */
    public function availableOrders(Request $request): JsonResponse
    {
        $orders = Order::with('orderProducts.product', 'user')
            ->where('status', OrderStatus::Processing)
            ->whereNull('driver_id')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($orders);
    }

    /**
     * Driver picks up an order.
     */
    public function pickupOrder(Request $request, Order $order): JsonResponse
    {
        if ($order->status !== OrderStatus::Processing) {
            return response()->json([
                'message' => 'Order is not ready for pickup.',
            ], 422);
        }

        if ($order->driver_id && $order->driver_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Order is already assigned to another driver.',
            ], 422);
        }

        $order->update([
            'status' => OrderStatus::Shipping,
            'driver_id' => $request->user()->id,
        ]);

        event(new OrderStatusUpdated($order->id, OrderStatus::Shipping->value, $request->user()->name));

        return response()->json([
            'message' => 'Order picked up successfully.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'driver_id' => $order->driver_id,
            ],
        ]);
    }

    /**
     * Driver delivers an order.
     */
    public function deliverOrder(Request $request, Order $order): JsonResponse
    {
        if ($order->status !== OrderStatus::Shipping) {
            return response()->json([
                'message' => 'Order is not in shipping status.',
            ], 422);
        }

        if ($order->driver_id !== $request->user()->id) {
            return response()->json([
                'message' => 'This order is not assigned to you.',
            ], 403);
        }

        $order->update(['status' => OrderStatus::Delivered]);

        event(new OrderStatusUpdated($order->id, OrderStatus::Delivered->value, $request->user()->name));

        return response()->json([
            'message' => 'Order delivered successfully.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
            ],
        ]);
    }

    /**
     * List completed (delivered) orders for the authenticated driver — order history.
     */
    public function history(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->driverOrders()
            ->with('orderProducts.product', 'user')
            ->where('status', OrderStatus::Delivered)
            ->orderByDesc('updated_at')
            ->paginate(15);

        return response()->json($orders);
    }

    /**
     * Get the driver's profile with accumulated rating.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'average_rating' => $user->average_rating,
            'total_ratings' => $user->total_ratings,
            'total_deliveries' => $user->driverOrders()
                ->where('status', OrderStatus::Delivered)
                ->count(),
        ]);
    }
}

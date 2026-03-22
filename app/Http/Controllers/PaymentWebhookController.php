<?php

namespace App\Http\Controllers;

use App\Events\PaymentStatusUpdated;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle Midtrans payment webhook notification.
     */
    public function handle(Request $request, PaymentService $paymentService): JsonResponse
    {
        $payload = $request->all();

        Log::info('Midtrans webhook received', $payload);

        // Verify signature
        $result = $paymentService->verifyNotification($payload);

        if (! $result['verified']) {
            Log::warning('Midtrans webhook signature verification failed', $payload);
            // Return 200 so Midtrans test button doesn't fail, but we don't process the transaction.
            return response()->json(['message' => 'Invalid signature, but acknowledging receipt for Midtrans test.'], 200);
        }

        $paymentReference = $result['order_id'];
        $transactionStatus = $result['transaction_status'];
        $fraudStatus = $result['fraud_status'];

        // Find order by payment_reference
        $order = Order::where('payment_reference', $paymentReference)->first();

        if (! $order) {
            Log::warning('Order not found for payment reference', [
                'payment_reference' => $paymentReference,
            ]);
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Idempotency: skip if already in a terminal state
        if (in_array($order->payment_status, ['paid', 'failed'])) {
            return response()->json(['message' => 'Already processed.']);
        }

        // Map Midtrans transaction status to our payment/order status
        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept' || $transactionStatus === 'settlement') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                ]);
            }
        } elseif ($transactionStatus === 'pending') {
            $order->update([
                'payment_status' => 'unpaid',
                'status' => 'pending',
            ]);
        } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);
        } elseif ($transactionStatus === 'expire') {
            $order->update([
                'payment_status' => 'expired',
                'status' => 'cancelled',
            ]);
        }

        event(new PaymentStatusUpdated($order->id, $order->payment_status));

        Log::info('Midtrans webhook processed', [
            'order_id' => $order->id,
            'payment_status' => $order->payment_status,
            'status' => $order->status,
        ]);

        return response()->json(['message' => 'Webhook processed successfully.']);
    }
}

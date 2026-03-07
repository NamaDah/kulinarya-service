<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private string $serverKey;
    private string $clientKey;
    private string $snapUrl;
    private bool $isProduction;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->clientKey = config('services.midtrans.client_key');
        $this->isProduction = config('services.midtrans.is_production');
        $this->snapUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Create a Midtrans Snap transaction for the given order.
     *
     * @return array{snap_token: string, redirect_url: string}
     */
    public function createTransaction(Order $order): array
    {
        $order->load('orderProducts.product');

        $exchangeRate = 16000; // 1 USD = 16,000 IDR

        $itemDetails = $order->orderProducts->map(function ($item) use ($exchangeRate) {
            return [
                'id' => (string) $item->product_id,
                'price' => (int) round($item->unit_price * $exchangeRate),
                'quantity' => $item->quantity,
                'name' => substr($item->product->name ?? 'Product #' . $item->product_id, 0, 50),
            ];
        })->toArray();

        $grossAmount = array_reduce($itemDetails, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $payload = [
            'transaction_details' => [
                'order_id' => 'KUL-' . $order->id . '-' . time(),
                'address' => $order->address,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $order->user->name ?? 'Customer',
                'email' => $order->user->email ?? '',
            ],
        ];

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->post($this->snapUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();

                $order->update([
                    'snap_token' => $data['token'] ?? null,
                    'payment_reference' => $payload['transaction_details']['order_id'],
                ]);

                return [
                    'snap_token' => $data['token'] ?? '',
                    'redirect_url' => $data['redirect_url'] ?? '',
                ];
            }

            Log::error('Midtrans Snap API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['snap_token' => '', 'redirect_url' => ''];
        } catch (\Exception $e) {
            Log::error('Midtrans Snap API exception', [
                'message' => $e->getMessage(),
            ]);

            return ['snap_token' => '', 'redirect_url' => ''];
        }
    }

    /**
     * Verify a webhook notification from Midtrans.
     *
     * @return array{verified: bool, order_id: string, transaction_status: string, fraud_status: string}
     */
    public function verifyNotification(array $payload): array
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        // Verify signature: SHA512(order_id + status_code + gross_amount + server_key)
        $expectedSignature = hash(
            'sha512',
            $orderId . $statusCode . $grossAmount . $this->serverKey
        );

        return [
            'verified' => $signatureKey === $expectedSignature,
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
        ];
    }

    /**
     * Get the Midtrans client key for frontend use.
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }
}

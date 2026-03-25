<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = App\Models\Order::first();
if (!$order) {
    echo "No order found!\n";
    exit;
}

// Assume we are the customer sending to the driver
$user = $order->user;
if (!$user) {
    echo "Order has no user.\n";
    exit;
}

$token = $user->createToken('cli-test')->plainTextToken;
$receiverId = $order->driver_id ?? App\Models\User::where('role', 'driver')->first()->id ?? 2;

echo "ORDER_ID={$order->id}\n";
echo "RECEIVER_ID={$receiverId}\n";
echo "TOKEN={$token}\n";

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $order = App\Models\Order::first();
    $userId = $order->user_id;
    $receiverId = $order->driver_id ?? App\Models\User::where('role', 'driver')->first()->id ?? 2;

    $message = $order->messages()->create([
        'sender_id' => $userId,
        'receiver_id' => $receiverId,
        'message' => 'test message from script',
    ]);
    
    $message->load('sender:id,name,role');
    echo "Message created, ID: {$message->id}\n";
    
    event(new App\Events\MessageSent($message));
    echo "Event broadcasted successfully\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString();
} catch (\Error $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}

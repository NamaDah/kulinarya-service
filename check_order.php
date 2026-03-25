<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$o = App\Models\Order::find(18);
if ($o) {
    echo "STATUS: {$o->status->value}\n";
    echo "DRIVER_ID: {$o->driver_id}\n";
} else {
    echo "ORDER 18 NOT FOUND\n";
}

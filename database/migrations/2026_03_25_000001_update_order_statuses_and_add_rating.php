<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert status from ENUM to STRING
        DB::statement("ALTER TABLE orders MODIFY COLUMN status VARCHAR(255) DEFAULT 'pending'");

        // Migrate existing data
        DB::table('orders')->where('status', 'shipped')->update(['status' => 'shipping']);
        DB::table('orders')->where('status', 'delivered')->update(['status' => 'done']);

        // Convert payment_status from ENUM to STRING
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status VARCHAR(255) DEFAULT 'unpaid'");

        // Add rating and driver_id
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->after('total_amount');
            $table->foreignId('driver_id')->nullable()->after('rating')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['rating', 'driver_id']);
        });

        // Revert status data
        DB::table('orders')->where('status', 'shipping')->update(['status' => 'shipped']);
        DB::table('orders')->where('status', 'done')->update(['status' => 'delivered']);

        // Revert to ENUM types
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('unpaid', 'paid', 'expired', 'failed') DEFAULT 'unpaid'");
    }
};

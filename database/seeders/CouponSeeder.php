<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount',
                'description' => 'Get 10% off on your first order',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'min_order_amount' => 200.00,
                'max_discount_amount' => 100.00,
                'usage_limit' => 1000,
                'is_active' => true,
            ],
            [
                'code' => 'FIESTA50',
                'name' => 'Fiesta Special',
                'description' => 'Php 50 off on orders above Php 500',
                'discount_type' => 'fixed',
                'discount_value' => 50.00,
                'min_order_amount' => 500.00,
                'usage_limit' => 500,
                'is_active' => true,
            ],
            [
                'code' => 'FREEDEL',
                'name' => 'Free Delivery',
                'description' => 'Free delivery on orders above Php 300',
                'discount_type' => 'fixed',
                'discount_value' => 50.00,
                'min_order_amount' => 300.00,
                'is_active' => true,
            ],
            [
                'code' => 'KAIN20',
                'name' => 'Kain Na!',
                'description' => '20% off on selected items',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'min_order_amount' => 400.00,
                'max_discount_amount' => 200.00,
                'usage_limit' => 200,
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::firstOrCreate(['code' => $coupon['code']], $coupon);
        }
    }
}

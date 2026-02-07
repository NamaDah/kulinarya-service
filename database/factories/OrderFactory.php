<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 100, 2000);
        $discountAmount = fake()->optional(0.3)->randomFloat(2, 10, 100) ?? 0;
        $deliveryFee = fake()->randomFloat(2, 0, 100);
        $total = $subtotal - $discountAmount + $deliveryFee;

        return [
            'order_number' => 'ORD-' . strtoupper(fake()->unique()->lexify('????????')),
            'user_id' => User::factory(),
            'address_id' => fake()->optional(0.8)->randomElement([Address::factory()]),
            'coupon_id' => null,
            'status' => fake()->randomElement(['pending', 'confirmed', 'preparing', 'ready', 'delivering', 'completed', 'cancelled']),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'payment_method' => fake()->randomElement(['cash', 'gcash', 'credit_card', 'bank_transfer']),
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'notes' => fake()->optional(0.3)->sentence(),
            'confirmed_at' => fake()->optional(0.6)->dateTimeBetween('-1 month', 'now'),
            'completed_at' => fake()->optional(0.4)->dateTimeBetween('-1 week', 'now'),
        ];
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
            'confirmed_at' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
            'confirmed_at' => now()->subDays(2),
            'completed_at' => now()->subDay(),
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'confirmed_at' => null,
            'completed_at' => null,
        ]);
    }
}

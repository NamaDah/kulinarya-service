<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discountType = fake()->randomElement(['fixed', 'percentage']);
        $discountValue = $discountType === 'fixed'
            ? fake()->randomFloat(2, 10, 100)
            : fake()->randomFloat(2, 5, 50);

        return [
            'code' => strtoupper(fake()->unique()->lexify('??????')),
            'name' => fake()->words(2, true) . ' Discount',
            'description' => fake()->sentence(),
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'min_order_amount' => fake()->optional(0.7)->randomFloat(2, 100, 500),
            'max_discount_amount' => $discountType === 'percentage' ? fake()->optional(0.8)->randomFloat(2, 50, 200) : null,
            'usage_limit' => fake()->optional(0.6)->numberBetween(10, 100),
            'used_count' => 0,
            'valid_from' => fake()->optional(0.5)->dateTimeBetween('-1 month', 'now'),
            'valid_until' => fake()->optional(0.5)->dateTimeBetween('now', '+3 months'),
            'is_active' => fake()->boolean(80),
        ];
    }

    /**
     * Indicate that the coupon is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'valid_from' => now()->subDays(7),
            'valid_until' => now()->addMonth(),
        ]);
    }

    /**
     * Indicate that the coupon is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'valid_until' => now()->subDay(),
        ]);
    }
}

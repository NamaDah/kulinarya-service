<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_id' => Order::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'review' => fake()->optional(0.7)->paragraph(),
        ];
    }

    /**
     * Indicate a positive rating.
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(4, 5),
            'review' => fake()->randomElement([
                'Excellent food and service!',
                'Will definitely order again!',
                'Best Filipino food in town!',
                'Amazing taste, fast delivery!',
            ]),
        ]);
    }

    /**
     * Indicate a negative rating.
     */
    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(1, 2),
            'review' => fake()->randomElement([
                'Food arrived cold.',
                'Long waiting time.',
                'Portion was too small.',
                'Not as expected.',
            ]),
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Appetizers' => 'Delicious starters to begin your meal',
            'Main Dishes' => 'Hearty main course options',
            'Desserts' => 'Sweet treats to end your meal',
            'Beverages' => 'Refreshing drinks and beverages',
            'Filipino Classics' => 'Traditional Filipino favorites',
            'Seafood' => 'Fresh catches from the sea',
            'Grilled Items' => 'Smoky and flavorful grilled dishes',
            'Soups' => 'Warm and comforting soups',
        ];

        $name = fake()->unique()->randomElement(array_keys($categories));

        return [
            'name' => $name,
            'description' => $categories[$name],
        ];
    }
}

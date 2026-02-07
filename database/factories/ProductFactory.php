<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filipinoFoods = [
            'Adobo' => 'Classic Filipino braised meat in soy sauce and vinegar',
            'Sinigang' => 'Sour Filipino soup with pork or shrimp',
            'Lumpia' => 'Filipino spring rolls',
            'Kare-Kare' => 'Oxtail stew with peanut sauce',
            'Lechon Kawali' => 'Crispy pan-fried pork belly',
            'Sisig' => 'Sizzling chopped pork',
            'Pancit Canton' => 'Stir-fried egg noodles',
            'Bicol Express' => 'Spicy pork in coconut milk',
            'Laing' => 'Taro leaves in coconut milk',
            'Tinola' => 'Ginger chicken soup',
            'Caldereta' => 'Tomato-based meat stew',
            'Menudo' => 'Pork and liver stew',
            'Paksiw na Bangus' => 'Vinegar-cooked milkfish',
            'Inihaw na Liempo' => 'Grilled pork belly',
            'Halo-Halo' => 'Shaved ice dessert with various toppings',
            'Leche Flan' => 'Caramel custard',
            'Buko Pandan' => 'Coconut pandan dessert',
            'Turon' => 'Banana spring roll',
        ];

        $name = fake()->unique()->randomElement(array_keys($filipinoFoods));

        return [
            'category_id' => Category::factory(),
            'code' => strtoupper(fake()->unique()->lexify('PROD-????')),
            'name' => $name,
            'description' => $filipinoFoods[$name],
            'image_url' => fake()->optional(0.8)->imageUrl(640, 480, 'food'),
            'price' => fake()->randomFloat(2, 50, 500),
            'is_available' => fake()->boolean(90),
            'stock' => fake()->optional(0.7)->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the product is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => true,
            'stock' => fake()->numberBetween(10, 100),
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
            'stock' => 0,
        ]);
    }
}

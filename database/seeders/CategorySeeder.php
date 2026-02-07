<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Appetizers', 'description' => 'Delicious starters to begin your meal'],
            ['name' => 'Main Dishes', 'description' => 'Hearty main course options'],
            ['name' => 'Desserts', 'description' => 'Sweet treats to end your meal'],
            ['name' => 'Beverages', 'description' => 'Refreshing drinks and beverages'],
            ['name' => 'Filipino Classics', 'description' => 'Traditional Filipino favorites'],
            ['name' => 'Seafood', 'description' => 'Fresh catches from the sea'],
            ['name' => 'Grilled Items', 'description' => 'Smoky and flavorful grilled dishes'],
            ['name' => 'Soups', 'description' => 'Warm and comforting soups'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}

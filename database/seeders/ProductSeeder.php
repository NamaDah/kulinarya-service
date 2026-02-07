<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Filipino Classics
            ['category' => 'Filipino Classics', 'code' => 'PROD-ADOBO', 'name' => 'Chicken Adobo', 'description' => 'Classic Filipino braised chicken in soy sauce and vinegar', 'price' => 180.00],
            ['category' => 'Filipino Classics', 'code' => 'PROD-SINIGANG', 'name' => 'Sinigang na Baboy', 'description' => 'Sour pork soup with tamarind and vegetables', 'price' => 220.00],
            ['category' => 'Filipino Classics', 'code' => 'PROD-KAREKARE', 'name' => 'Kare-Kare', 'description' => 'Oxtail stew with peanut sauce', 'price' => 350.00],
            ['category' => 'Filipino Classics', 'code' => 'PROD-SISIG', 'name' => 'Sizzling Sisig', 'description' => 'Sizzling chopped pork face with egg', 'price' => 195.00],
            
            // Appetizers
            ['category' => 'Appetizers', 'code' => 'PROD-LUMPIA', 'name' => 'Lumpiang Shanghai', 'description' => 'Crispy Filipino spring rolls', 'price' => 120.00],
            ['category' => 'Appetizers', 'code' => 'PROD-TOKWA', 'name' => 'Tokwa\'t Baboy', 'description' => 'Fried tofu and pork with soy vinegar dip', 'price' => 95.00],
            
            // Main Dishes
            ['category' => 'Main Dishes', 'code' => 'PROD-LECHON', 'name' => 'Lechon Kawali', 'description' => 'Crispy pan-fried pork belly', 'price' => 280.00],
            ['category' => 'Main Dishes', 'code' => 'PROD-BICOL', 'name' => 'Bicol Express', 'description' => 'Spicy pork in coconut milk', 'price' => 185.00],
            ['category' => 'Main Dishes', 'code' => 'PROD-CALDERETA', 'name' => 'Beef Caldereta', 'description' => 'Tomato-based beef stew', 'price' => 320.00],
            
            // Grilled Items
            ['category' => 'Grilled Items', 'code' => 'PROD-LIEMPO', 'name' => 'Inihaw na Liempo', 'description' => 'Grilled marinated pork belly', 'price' => 250.00],
            ['category' => 'Grilled Items', 'code' => 'PROD-INASAL', 'name' => 'Chicken Inasal', 'description' => 'Grilled chicken marinated in vinegar and annatto', 'price' => 180.00],
            
            // Seafood
            ['category' => 'Seafood', 'code' => 'PROD-PAKSIW', 'name' => 'Paksiw na Bangus', 'description' => 'Vinegar-cooked milkfish', 'price' => 200.00],
            ['category' => 'Seafood', 'code' => 'PROD-SINIGANGHIPON', 'name' => 'Sinigang na Hipon', 'description' => 'Sour shrimp soup with vegetables', 'price' => 280.00],
            
            // Soups
            ['category' => 'Soups', 'code' => 'PROD-TINOLA', 'name' => 'Chicken Tinola', 'description' => 'Ginger chicken soup with green papaya', 'price' => 180.00],
            ['category' => 'Soups', 'code' => 'PROD-BULALO', 'name' => 'Bulalo', 'description' => 'Beef bone marrow soup', 'price' => 380.00],
            
            // Desserts
            ['category' => 'Desserts', 'code' => 'PROD-HALOHALO', 'name' => 'Halo-Halo', 'description' => 'Shaved ice dessert with various toppings', 'price' => 120.00],
            ['category' => 'Desserts', 'code' => 'PROD-LECHE', 'name' => 'Leche Flan', 'description' => 'Creamy caramel custard', 'price' => 85.00],
            ['category' => 'Desserts', 'code' => 'PROD-TURON', 'name' => 'Turon', 'description' => 'Banana spring roll with jackfruit', 'price' => 60.00],
            
            // Beverages
            ['category' => 'Beverages', 'code' => 'PROD-SAGO', 'name' => 'Sago\'t Gulaman', 'description' => 'Sweet tapioca and gelatin drink', 'price' => 50.00],
            ['category' => 'Beverages', 'code' => 'PROD-BUKO', 'name' => 'Buko Juice', 'description' => 'Fresh young coconut water', 'price' => 65.00],
            ['category' => 'Beverages', 'code' => 'PROD-CALAMANSI', 'name' => 'Calamansi Juice', 'description' => 'Refreshing Filipino citrus drink', 'price' => 45.00],
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            
            Product::firstOrCreate(
                ['code' => $productData['code']],
                [
                    'category_id' => $category?->id,
                    'code' => $productData['code'],
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'is_available' => true,
                    'stock' => rand(10, 100),
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        

        // ── Categories ──────────────────────────────────────────────
        $chinese = Category::create([
            'name' => 'Chinese',
            'slug' => 'chinese',
            'description' => 'Authentic Chinese cuisine featuring bold flavors, wok-fired dishes, and time-honored recipes from across China\'s diverse culinary regions.',
            'image_url' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=800',
            'cuisine_type' => 'chinese',
        ]);

        $japanese = Category::create([
            'name' => 'Japanese',
            'slug' => 'japanese',
            'description' => 'Elegant Japanese cooking emphasizing fresh ingredients, delicate presentation, and the harmony of umami flavors.',
            'image_url' => 'https://images.unsplash.com/photo-1580822184713-fc5400e7fe10?w=800',
            'cuisine_type' => 'japanese',
        ]);

        $korean = Category::create([
            'name' => 'Korean',
            'slug' => 'korean',
            'description' => 'Vibrant Korean fare bursting with fermented flavors, spicy gochujang, and communal dining traditions.',
            'image_url' => 'https://images.unsplash.com/photo-1590301157890-4810ed352733?w=800',
            'cuisine_type' => 'korean',
        ]);

        // ── Chinese Products ────────────────────────────────────────
        $soySauce = Product::create([
            'category_id' => $chinese->id,
            'name' => 'Premium Soy Sauce',
            'slug' => 'premium-soy-sauce',
            'description' => 'Naturally brewed dark soy sauce aged for 6 months. Rich, deep umami flavor perfect for stir-fries and marinades.',
            'price' => 8.99,
            'image_url' => 'https://images.unsplash.com/photo-1614563637806-1d0e645e0940?w=600',
            'unit' => 'bottle',
        ]);

        $oysterSauce = Product::create([
            'category_id' => $chinese->id,
            'name' => 'Oyster Sauce',
            'slug' => 'oyster-sauce',
            'description' => 'Thick, savory oyster extract sauce. A must-have for Cantonese stir-fries and glazed vegetables.',
            'price' => 6.49,
            'image_url' => 'https://images.unsplash.com/photo-1472476443507-c7a5948772fc?w=600',
            'unit' => 'bottle',
        ]);

        $sesamoOil = Product::create([
            'category_id' => $chinese->id,
            'name' => 'Toasted Sesame Oil',
            'slug' => 'toasted-sesame-oil',
            'description' => 'Pure toasted sesame oil with a deep, nutty aroma. Use as a finishing oil for maximum flavor.',
            'price' => 7.99,
            'image_url' => 'https://images.unsplash.com/photo-1474979266404-7f28db35f3e5?w=600',
            'unit' => 'bottle',
        ]);

        $riceVinegar = Product::create([
            'category_id' => $chinese->id,
            'name' => 'Chinkiang Black Vinegar',
            'slug' => 'chinkiang-black-vinegar',
            'description' => 'Aged black vinegar from Zhenjiang with complex, malty sweetness. Essential for dumplings and braised dishes.',
            'price' => 5.99,
            'image_url' => 'https://images.unsplash.com/photo-1609501676725-7186f017a4b7?w=600',
            'unit' => 'bottle',
        ]);

        $fiveSpice = Product::create([
            'category_id' => $chinese->id,
            'name' => 'Five Spice Powder',
            'slug' => 'five-spice-powder',
            'description' => 'Classic Chinese blend of star anise, cloves, cinnamon, Sichuan pepper, and fennel seeds.',
            'price' => 4.99,
            'image_url' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=600',
            'unit' => 'jar',
        ]);

        $wokNoodles = Product::create([
            'category_id' => $chinese->id,
            'name' => 'Fresh Wok Noodles',
            'slug' => 'fresh-wok-noodles',
            'description' => 'Thick, chewy wheat noodles perfect for lo mein and chow mein. Ready to cook in 3 minutes.',
            'price' => 3.49,
            'image_url' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=600',
            'unit' => 'pack',
        ]);

        // ── Japanese Products ───────────────────────────────────────
        $misoWhite = Product::create([
            'category_id' => $japanese->id,
            'name' => 'White Miso Paste',
            'slug' => 'white-miso-paste',
            'description' => 'Mild, slightly sweet shiro miso made from fermented soybeans and rice. Perfect for miso soup and glazes.',
            'price' => 9.99,
            'image_url' => 'https://images.unsplash.com/photo-1607301405390-d831c242f59b?w=600',
            'unit' => 'tub',
        ]);

        $dashi = Product::create([
            'category_id' => $japanese->id,
            'name' => 'Dashi Stock Powder',
            'slug' => 'dashi-stock-powder',
            'description' => 'Instant bonito and kelp dashi powder. The umami foundation for all Japanese soups and sauces.',
            'price' => 6.99,
            'image_url' => 'https://images.unsplash.com/photo-1555126634-323283e090fa?w=600',
            'unit' => 'pack',
        ]);

        $noriSheets = Product::create([
            'category_id' => $japanese->id,
            'name' => 'Roasted Nori Sheets',
            'slug' => 'roasted-nori-sheets',
            'description' => 'Premium grade roasted seaweed sheets for sushi rolls, onigiri, and garnishes.',
            'price' => 7.49,
            'image_url' => 'https://images.unsplash.com/photo-1553621042-f6e147245754?w=600',
            'unit' => 'pack',
        ]);

        $sushiRice = Product::create([
            'category_id' => $japanese->id,
            'name' => 'Japanese Short-Grain Rice',
            'slug' => 'japanese-short-grain-rice',
            'description' => 'Premium Koshihikari rice with a sticky, glossy texture. Ideal for sushi and donburi.',
            'price' => 12.99,
            'image_url' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600',
            'unit' => 'bag',
        ]);

        $mirin = Product::create([
            'category_id' => $japanese->id,
            'name' => 'Hon Mirin',
            'slug' => 'hon-mirin',
            'description' => 'Authentic sweet rice wine for teriyaki, simmered dishes, and glazes. Adds a beautiful sheen.',
            'price' => 8.49,
            'image_url' => 'https://images.unsplash.com/photo-1514362545857-3bc16c8c7f1b?w=600',
            'unit' => 'bottle',
        ]);

        $panko = Product::create([
            'category_id' => $japanese->id,
            'name' => 'Panko Breadcrumbs',
            'slug' => 'panko-breadcrumbs',
            'description' => 'Light, flaky Japanese breadcrumbs for ultra-crispy tonkatsu, korokke, and fried shrimp.',
            'price' => 4.49,
            'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600',
            'unit' => 'pack',
        ]);

        // ── Korean Products ─────────────────────────────────────────
        $gochujang = Product::create([
            'category_id' => $korean->id,
            'name' => 'Gochujang Paste',
            'slug' => 'gochujang-paste',
            'description' => 'Fermented red chili paste with deep, sweet heat. The soul of Korean cooking.',
            'price' => 8.99,
            'image_url' => 'https://images.unsplash.com/photo-1635865165118-917ed9e20bab?w=600',
            'unit' => 'tub',
        ]);

        $gochugaru = Product::create([
            'category_id' => $korean->id,
            'name' => 'Gochugaru Chili Flakes',
            'slug' => 'gochugaru-chili-flakes',
            'description' => 'Sun-dried Korean chili flakes with a smoky, fruity heat. Essential for kimchi and stews.',
            'price' => 9.49,
            'image_url' => 'https://images.unsplash.com/photo-1599909533601-aa23a627c7a4?w=600',
            'unit' => 'bag',
        ]);

        $kimchi = Product::create([
            'category_id' => $korean->id,
            'name' => 'Artisanal Napa Kimchi',
            'slug' => 'artisanal-napa-kimchi',
            'description' => 'Traditionally fermented napa cabbage kimchi with garlic, ginger, and gochugaru. Aged 4 weeks.',
            'price' => 11.99,
            'image_url' => 'https://images.unsplash.com/photo-1583224964978-2257b960c3f3?w=600',
            'unit' => 'jar',
        ]);

        $doenjang = Product::create([
            'category_id' => $korean->id,
            'name' => 'Doenjang Soybean Paste',
            'slug' => 'doenjang-soybean-paste',
            'description' => 'Rustic fermented soybean paste with bold, earthy umami. Key ingredient for jjigae stews.',
            'price' => 7.49,
            'image_url' => 'https://images.unsplash.com/photo-1590301157890-4810ed352733?w=600',
            'unit' => 'tub',
        ]);

        $koreanRiceCakes = Product::create([
            'category_id' => $korean->id,
            'name' => 'Tteok Rice Cakes',
            'slug' => 'tteok-rice-cakes',
            'description' => 'Chewy, cylindrical rice cakes for tteokbokki and soups. Made from 100% glutinous rice flour.',
            'price' => 5.99,
            'image_url' => 'https://images.unsplash.com/photo-1635363638580-c2809d049eee?w=600',
            'unit' => 'pack',
        ]);

        $sesamSeeds = Product::create([
            'category_id' => $korean->id,
            'name' => 'Roasted Sesame Seeds',
            'slug' => 'roasted-sesame-seeds',
            'description' => 'Golden toasted sesame seeds for garnishing bibimbap, japchae, and banchan.',
            'price' => 3.99,
            'image_url' => 'https://images.unsplash.com/photo-1590165482129-1b8b27698780?w=600',
            'unit' => 'jar',
        ]);

        // ── Chinese Recipes ─────────────────────────────────────────
        $kungPao = Recipe::create([
            'category_id' => $chinese->id,
            'title' => 'Kung Pao Chicken',
            'slug' => 'kung-pao-chicken',
            'description' => 'A fiery Sichuan classic: tender chicken wok-tossed with dried chilies, crunchy peanuts, and a tangy-sweet sauce. The hallmark numbing spice of Sichuan peppercorns elevates every bite.',
            'image_url' => 'https://images.unsplash.com/photo-1525755662778-989d0524087e?w=800',
            'video_url' => 'https://www.youtube.com/embed/Ot69v0JKMqs',
            'prep_time_minutes' => 20,
            'cook_time_minutes' => 15,
            'servings' => 4,
            'cuisine_type' => 'chinese',
        ]);

        foreach ([
            ['name' => 'Chicken Thigh', 'quantity' => '500', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Soy Sauce', 'quantity' => '3', 'unit' => 'tbsp', 'product_id' => $soySauce->id],
            ['name' => 'Chinkiang Black Vinegar', 'quantity' => '2', 'unit' => 'tbsp', 'product_id' => $riceVinegar->id],
            ['name' => 'Toasted Sesame Oil', 'quantity' => '1', 'unit' => 'tbsp', 'product_id' => $sesamoOil->id],
            ['name' => 'Dried Red Chilies', 'quantity' => '10', 'unit' => 'pieces', 'product_id' => null],
            ['name' => 'Roasted Peanuts', 'quantity' => '80', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Garlic', 'quantity' => '4', 'unit' => 'cloves', 'product_id' => null],
            ['name' => 'Green Onions', 'quantity' => '3', 'unit' => 'stalks', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $kungPao->id], $ingredient));
        }

        $charSiu = Recipe::create([
            'category_id' => $chinese->id,
            'title' => 'Char Siu Pork',
            'slug' => 'char-siu-pork',
            'description' => 'Cantonese BBQ pork glazed with a sticky-sweet mixture of honey, hoisin, and five-spice. Roasted until caramelized with beautiful charred edges.',
            'image_url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800',
            'video_url' => 'https://www.youtube.com/embed/eFBmiVlJvcs',
            'prep_time_minutes' => 30,
            'cook_time_minutes' => 45,
            'servings' => 6,
            'cuisine_type' => 'chinese',
        ]);

        foreach ([
            ['name' => 'Pork Shoulder', 'quantity' => '800', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Soy Sauce', 'quantity' => '4', 'unit' => 'tbsp', 'product_id' => $soySauce->id],
            ['name' => 'Five Spice Powder', 'quantity' => '1', 'unit' => 'tsp', 'product_id' => $fiveSpice->id],
            ['name' => 'Oyster Sauce', 'quantity' => '2', 'unit' => 'tbsp', 'product_id' => $oysterSauce->id],
            ['name' => 'Honey', 'quantity' => '3', 'unit' => 'tbsp', 'product_id' => null],
            ['name' => 'Hoisin Sauce', 'quantity' => '2', 'unit' => 'tbsp', 'product_id' => null],
            ['name' => 'Garlic', 'quantity' => '3', 'unit' => 'cloves', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $charSiu->id], $ingredient));
        }

        $chowMein = Recipe::create([
            'category_id' => $chinese->id,
            'title' => 'Classic Chow Mein',
            'slug' => 'classic-chow-mein',
            'description' => 'Wok-fried noodles tossed with crisp vegetables and a savory soy-sesame glaze. Ready in under 20 minutes for a satisfying weeknight dinner.',
            'image_url' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=800',
            'video_url' => 'https://www.youtube.com/embed/sOjMTFKYQjE',
            'prep_time_minutes' => 15,
            'cook_time_minutes' => 10,
            'servings' => 3,
            'cuisine_type' => 'chinese',
        ]);

        foreach ([
            ['name' => 'Fresh Wok Noodles', 'quantity' => '400', 'unit' => 'g', 'product_id' => $wokNoodles->id],
            ['name' => 'Soy Sauce', 'quantity' => '3', 'unit' => 'tbsp', 'product_id' => $soySauce->id],
            ['name' => 'Oyster Sauce', 'quantity' => '2', 'unit' => 'tbsp', 'product_id' => $oysterSauce->id],
            ['name' => 'Toasted Sesame Oil', 'quantity' => '1', 'unit' => 'tsp', 'product_id' => $sesamoOil->id],
            ['name' => 'Bean Sprouts', 'quantity' => '150', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Cabbage', 'quantity' => '200', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Carrots', 'quantity' => '2', 'unit' => 'medium', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $chowMein->id], $ingredient));
        }

        $mapoTofu = Recipe::create([
            'category_id' => $chinese->id,
            'title' => 'Mapo Tofu',
            'slug' => 'mapo-tofu',
            'description' => 'Silky tofu cubes swimming in a bubbling, numbing-spicy sauce of doubanjiang and ground pork. Topped with a shower of Sichuan pepper and scallions.',
            'image_url' => 'https://images.unsplash.com/photo-1582452919408-aca1e0c5e439?w=800',
            'video_url' => 'https://www.youtube.com/embed/ZfsZwwrTFD4',
            'prep_time_minutes' => 10,
            'cook_time_minutes' => 15,
            'servings' => 4,
            'cuisine_type' => 'chinese',
        ]);

        foreach ([
            ['name' => 'Silken Tofu', 'quantity' => '400', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Ground Pork', 'quantity' => '200', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Soy Sauce', 'quantity' => '2', 'unit' => 'tbsp', 'product_id' => $soySauce->id],
            ['name' => 'Toasted Sesame Oil', 'quantity' => '1', 'unit' => 'tsp', 'product_id' => $sesamoOil->id],
            ['name' => 'Doubanjiang', 'quantity' => '2', 'unit' => 'tbsp', 'product_id' => null],
            ['name' => 'Garlic', 'quantity' => '3', 'unit' => 'cloves', 'product_id' => null],
            ['name' => 'Green Onions', 'quantity' => '2', 'unit' => 'stalks', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $mapoTofu->id], $ingredient));
        }

        // ── Japanese Recipes ────────────────────────────────────────
        $ramen = Recipe::create([
            'category_id' => $japanese->id,
            'title' => 'Tonkotsu Ramen',
            'slug' => 'tonkotsu-ramen',
            'description' => 'Rich, milky pork-bone broth simmered for 12 hours, served with springy noodles, chashu pork, a soft-boiled egg, and nori. The ultimate comfort bowl.',
            'image_url' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=800',
            'video_url' => 'https://www.youtube.com/embed/OL22PoP1Oqo',
            'prep_time_minutes' => 30,
            'cook_time_minutes' => 720,
            'servings' => 4,
            'cuisine_type' => 'japanese',
        ]);

        foreach ([
            ['name' => 'Pork Bones', 'quantity' => '1.5', 'unit' => 'kg', 'product_id' => null],
            ['name' => 'Dashi Stock Powder', 'quantity' => '2', 'unit' => 'tsp', 'product_id' => $dashi->id],
            ['name' => 'White Miso Paste', 'quantity' => '3', 'unit' => 'tbsp', 'product_id' => $misoWhite->id],
            ['name' => 'Roasted Nori Sheets', 'quantity' => '4', 'unit' => 'sheets', 'product_id' => $noriSheets->id],
            ['name' => 'Soy Sauce', 'quantity' => '4', 'unit' => 'tbsp', 'product_id' => $soySauce->id],
            ['name' => 'Ramen Noodles', 'quantity' => '400', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Soft-Boiled Eggs', 'quantity' => '4', 'unit' => 'pieces', 'product_id' => null],
            ['name' => 'Green Onions', 'quantity' => '3', 'unit' => 'stalks', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $ramen->id], $ingredient));
        }

        $teriyaki = Recipe::create([
            'category_id' => $japanese->id,
            'title' => 'Teriyaki Salmon',
            'slug' => 'teriyaki-salmon',
            'description' => 'Pan-seared salmon fillets glazed with a homemade teriyaki sauce of soy, mirin, and sake. Served over steamed rice with pickled ginger.',
            'image_url' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=800',
            'video_url' => 'https://www.youtube.com/embed/dBnGqpD7JME',
            'prep_time_minutes' => 10,
            'cook_time_minutes' => 15,
            'servings' => 2,
            'cuisine_type' => 'japanese',
        ]);

        foreach ([
            ['name' => 'Salmon Fillets', 'quantity' => '2', 'unit' => 'pieces', 'product_id' => null],
            ['name' => 'Soy Sauce', 'quantity' => '3', 'unit' => 'tbsp', 'product_id' => $soySauce->id],
            ['name' => 'Hon Mirin', 'quantity' => '3', 'unit' => 'tbsp', 'product_id' => $mirin->id],
            ['name' => 'Japanese Short-Grain Rice', 'quantity' => '300', 'unit' => 'g', 'product_id' => $sushiRice->id],
            ['name' => 'Toasted Sesame Oil', 'quantity' => '1', 'unit' => 'tsp', 'product_id' => $sesamoOil->id],
            ['name' => 'Ginger', 'quantity' => '1', 'unit' => 'thumb', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $teriyaki->id], $ingredient));
        }

        $katsuCurry = Recipe::create([
            'category_id' => $japanese->id,
            'title' => 'Chicken Katsu Curry',
            'slug' => 'chicken-katsu-curry',
            'description' => 'Crispy panko-crusted chicken cutlet served over fluffy rice and smothered in a golden Japanese curry sauce. Comfort food at its finest.',
            'image_url' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=800',
            'video_url' => 'https://www.youtube.com/embed/rL52J_W-n6c',
            'prep_time_minutes' => 25,
            'cook_time_minutes' => 30,
            'servings' => 4,
            'cuisine_type' => 'japanese',
        ]);

        foreach ([
            ['name' => 'Chicken Breast', 'quantity' => '4', 'unit' => 'pieces', 'product_id' => null],
            ['name' => 'Panko Breadcrumbs', 'quantity' => '200', 'unit' => 'g', 'product_id' => $panko->id],
            ['name' => 'Japanese Short-Grain Rice', 'quantity' => '400', 'unit' => 'g', 'product_id' => $sushiRice->id],
            ['name' => 'Soy Sauce', 'quantity' => '2', 'unit' => 'tbsp', 'product_id' => $soySauce->id],
            ['name' => 'Hon Mirin', 'quantity' => '1', 'unit' => 'tbsp', 'product_id' => $mirin->id],
            ['name' => 'Curry Roux', 'quantity' => '100', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Onion', 'quantity' => '2', 'unit' => 'medium', 'product_id' => null],
            ['name' => 'Carrots', 'quantity' => '2', 'unit' => 'medium', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $katsuCurry->id], $ingredient));
        }

        $misoSoup = Recipe::create([
            'category_id' => $japanese->id,
            'title' => 'Classic Miso Soup',
            'slug' => 'classic-miso-soup',
            'description' => 'A warm, soothing bowl of dashi broth enriched with white miso, silken tofu, wakame seaweed, and a sprinkle of green onions. Ready in 10 minutes.',
            'image_url' => 'https://images.unsplash.com/photo-1607301405390-d831c242f59b?w=800',
            'video_url' => 'https://www.youtube.com/embed/FDuc8tYR7bI',
            'prep_time_minutes' => 5,
            'cook_time_minutes' => 10,
            'servings' => 4,
            'cuisine_type' => 'japanese',
        ]);

        foreach ([
            ['name' => 'Dashi Stock Powder', 'quantity' => '1', 'unit' => 'tbsp', 'product_id' => $dashi->id],
            ['name' => 'White Miso Paste', 'quantity' => '4', 'unit' => 'tbsp', 'product_id' => $misoWhite->id],
            ['name' => 'Silken Tofu', 'quantity' => '200', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Wakame Seaweed', 'quantity' => '10', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Green Onions', 'quantity' => '2', 'unit' => 'stalks', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $misoSoup->id], $ingredient));
        }

        // ── Korean Recipes ──────────────────────────────────────────
        $bibimbap = Recipe::create([
            'category_id' => $korean->id,
            'title' => 'Bibimbap',
            'slug' => 'bibimbap',
            'description' => 'A vibrant bowl of steamed rice topped with seasoned vegetables, gochujang, a fried egg, and optional beef. Mix everything together for a symphony of flavors and textures.',
            'image_url' => 'https://images.unsplash.com/photo-1590301157890-4810ed352733?w=800',
            'video_url' => 'https://www.youtube.com/embed/6QQ67F8y2b8',
            'prep_time_minutes' => 30,
            'cook_time_minutes' => 20,
            'servings' => 2,
            'cuisine_type' => 'korean',
        ]);

        foreach ([
            ['name' => 'Steamed Rice', 'quantity' => '400', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Gochujang Paste', 'quantity' => '3', 'unit' => 'tbsp', 'product_id' => $gochujang->id],
            ['name' => 'Toasted Sesame Oil', 'quantity' => '2', 'unit' => 'tbsp', 'product_id' => $sesamoOil->id],
            ['name' => 'Roasted Sesame Seeds', 'quantity' => '1', 'unit' => 'tbsp', 'product_id' => $sesamSeeds->id],
            ['name' => 'Spinach', 'quantity' => '150', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Bean Sprouts', 'quantity' => '100', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Carrots', 'quantity' => '1', 'unit' => 'medium', 'product_id' => null],
            ['name' => 'Eggs', 'quantity' => '2', 'unit' => 'pieces', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $bibimbap->id], $ingredient));
        }

        $tteokbokki = Recipe::create([
            'category_id' => $korean->id,
            'title' => 'Tteokbokki',
            'slug' => 'tteokbokki',
            'description' => 'Chewy rice cakes simmered in a fiery, sweet gochujang sauce with fish cakes and scallions. Korea\'s most beloved street food.',
            'image_url' => 'https://images.unsplash.com/photo-1635363638580-c2809d049eee?w=800',
            'video_url' => 'https://www.youtube.com/embed/DwkGTBH4YRE',
            'prep_time_minutes' => 10,
            'cook_time_minutes' => 20,
            'servings' => 3,
            'cuisine_type' => 'korean',
        ]);

        foreach ([
            ['name' => 'Tteok Rice Cakes', 'quantity' => '400', 'unit' => 'g', 'product_id' => $koreanRiceCakes->id],
            ['name' => 'Gochujang Paste', 'quantity' => '3', 'unit' => 'tbsp', 'product_id' => $gochujang->id],
            ['name' => 'Gochugaru Chili Flakes', 'quantity' => '1', 'unit' => 'tbsp', 'product_id' => $gochugaru->id],
            ['name' => 'Soy Sauce', 'quantity' => '1', 'unit' => 'tbsp', 'product_id' => $soySauce->id],
            ['name' => 'Fish Cakes', 'quantity' => '200', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Green Onions', 'quantity' => '3', 'unit' => 'stalks', 'product_id' => null],
            ['name' => 'Sugar', 'quantity' => '1', 'unit' => 'tbsp', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $tteokbokki->id], $ingredient));
        }

        $kimchiJjigae = Recipe::create([
            'category_id' => $korean->id,
            'title' => 'Kimchi Jjigae',
            'slug' => 'kimchi-jjigae',
            'description' => 'A bubbling, hearty stew of well-fermented kimchi, pork belly, tofu, and vegetables. Best enjoyed sizzling-hot with a bowl of steamed rice.',
            'image_url' => 'https://images.unsplash.com/photo-1583224964978-2257b960c3f3?w=800',
            'video_url' => 'https://www.youtube.com/embed/P-xFqEAPLqg',
            'prep_time_minutes' => 15,
            'cook_time_minutes' => 25,
            'servings' => 4,
            'cuisine_type' => 'korean',
        ]);

        foreach ([
            ['name' => 'Artisanal Napa Kimchi', 'quantity' => '300', 'unit' => 'g', 'product_id' => $kimchi->id],
            ['name' => 'Pork Belly', 'quantity' => '200', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Doenjang Soybean Paste', 'quantity' => '1', 'unit' => 'tbsp', 'product_id' => $doenjang->id],
            ['name' => 'Gochugaru Chili Flakes', 'quantity' => '1', 'unit' => 'tbsp', 'product_id' => $gochugaru->id],
            ['name' => 'Tofu', 'quantity' => '200', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Green Onions', 'quantity' => '2', 'unit' => 'stalks', 'product_id' => null],
            ['name' => 'Garlic', 'quantity' => '3', 'unit' => 'cloves', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $kimchiJjigae->id], $ingredient));
        }

        $japchae = Recipe::create([
            'category_id' => $korean->id,
            'title' => 'Japchae',
            'slug' => 'japchae',
            'description' => 'Glass noodles stir-fried with colorful vegetables, beef, and a sweet soy-sesame dressing. A celebratory dish served at every Korean festive table.',
            'image_url' => 'https://images.unsplash.com/photo-1498654896293-37aacf113fd9?w=800',
            'video_url' => 'https://www.youtube.com/embed/MlAewbv8fR0',
            'prep_time_minutes' => 20,
            'cook_time_minutes' => 15,
            'servings' => 4,
            'cuisine_type' => 'korean',
        ]);

        foreach ([
            ['name' => 'Sweet Potato Noodles', 'quantity' => '250', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Soy Sauce', 'quantity' => '3', 'unit' => 'tbsp', 'product_id' => $soySauce->id],
            ['name' => 'Toasted Sesame Oil', 'quantity' => '2', 'unit' => 'tbsp', 'product_id' => $sesamoOil->id],
            ['name' => 'Roasted Sesame Seeds', 'quantity' => '1', 'unit' => 'tbsp', 'product_id' => $sesamSeeds->id],
            ['name' => 'Spinach', 'quantity' => '100', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Bell Peppers', 'quantity' => '2', 'unit' => 'pieces', 'product_id' => null],
            ['name' => 'Beef Sirloin', 'quantity' => '150', 'unit' => 'g', 'product_id' => null],
            ['name' => 'Mushrooms', 'quantity' => '100', 'unit' => 'g', 'product_id' => null],
        ] as $ingredient) {
            RecipeIngredient::create(array_merge(['recipe_id' => $japchae->id], $ingredient));
        }
    }
}

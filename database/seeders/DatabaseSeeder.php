<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin User ───────────────────────────────────────────────────────
        User::create([
            'name'     => 'AniStore Admin',
            'email'    => 'admin@anistore.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // ─── Sample Customer ──────────────────────────────────────────────────
        User::create([
            'name'     => 'Naruto Fan',
            'email'    => 'customer@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

        // ─── Categories ───────────────────────────────────────────────────────
        $categories = [
            ['name' => 'Action Figures',    'description' => 'High-quality anime action figures and statues.',   'sort_order' => 1],
            ['name' => 'Apparel',           'description' => 'T-shirts, hoodies, and cosplay clothing.',         'sort_order' => 2],
            ['name' => 'Posters & Art',     'description' => 'Wall art, posters, and tapestries.',               'sort_order' => 3],
            ['name' => 'Accessories',       'description' => 'Keychains, pins, bags, and jewelry.',              'sort_order' => 4],
            ['name' => 'Manga & Books',     'description' => 'Manga volumes, art books, and light novels.',      'sort_order' => 5],
            ['name' => 'Home & Lifestyle',  'description' => 'Mugs, pillows, phone cases, and desk accessories.','sort_order' => 6],
        ];

        $createdCategories = [];
        foreach ($categories as $cat) {
            $createdCategories[] = Category::create([
                ...$cat,
                'slug'      => Str::slug($cat['name']),
                'is_active' => true,
            ]);
        }

        // ─── Sample Products ──────────────────────────────────────────────────
        $products = [
            // Action Figures
            [
                'name'        => 'Naruto Uzumaki PVC Figure — Sage Mode',
                'category'    => 'Action Figures',
                'description' => 'Highly detailed 20cm PVC figure of Naruto Uzumaki in his iconic Sage Mode pose. Includes a display base and comes in a collector\'s window box. A must-have for any Naruto fan.',
                'price'       => 1299.00,
                'compare_price' => 1599.00,
                'stock'       => 25,
                'sku'         => 'AF-NRT-001',
                'is_featured' => true,
                'tags'        => ['naruto', 'figure', 'sage mode', 'PVC'],
            ],
            [
                'name'        => 'Demon Slayer Tanjiro Kamado Figure',
                'category'    => 'Action Figures',
                'description' => 'Premium 18cm figure of Tanjiro Kamado from Demon Slayer with his Nichirin Blade and Water Breathing pose. Exquisite paint finish.',
                'price'       => 1499.00,
                'compare_price' => null,
                'stock'       => 15,
                'sku'         => 'AF-DS-001',
                'is_featured' => true,
                'tags'        => ['demon slayer', 'tanjiro', 'figure'],
            ],
            [
                'name'        => 'My Hero Academia All Might Statue',
                'category'    => 'Action Figures',
                'description' => '30cm collector statue of All Might in his Silver Age hero costume. Heavy resin construction with metallic finish details.',
                'price'       => 2999.00,
                'compare_price' => 3499.00,
                'stock'       => 8,
                'sku'         => 'AF-MHA-001',
                'is_featured' => false,
                'tags'        => ['my hero academia', 'all might', 'statue'],
            ],
            // Apparel
            [
                'name'        => 'Attack on Titan Survey Corps Hoodie',
                'category'    => 'Apparel',
                'description' => 'Premium quality pullover hoodie featuring the Survey Corps Wings of Freedom emblem. Made from 80% cotton, 20% polyester. Available in S–3XL.',
                'price'       => 999.00,
                'compare_price' => 1299.00,
                'stock'       => 50,
                'sku'         => 'APP-AOT-001',
                'is_featured' => true,
                'tags'        => ['attack on titan', 'hoodie', 'apparel', 'survey corps'],
            ],
            [
                'name'        => 'One Piece Luffy Oversized T-Shirt',
                'category'    => 'Apparel',
                'description' => 'Unisex oversized tee with original Luffy graphic print. 100% cotton, pre-shrunk. Machine washable. Sizes XS–4XL.',
                'price'       => 499.00,
                'compare_price' => null,
                'stock'       => 80,
                'sku'         => 'APP-OP-001',
                'is_featured' => false,
                'tags'        => ['one piece', 'luffy', 't-shirt'],
            ],
            // Posters
            [
                'name'        => 'Spirited Away A2 Art Print',
                'category'    => 'Posters & Art',
                'description' => 'High-quality A2 (420×594mm) art print on 200gsm matte paper. Frameable. Features the iconic bathhouse scene from Studio Ghibli\'s Spirited Away.',
                'price'       => 399.00,
                'compare_price' => null,
                'stock'       => 100,
                'sku'         => 'PST-GH-001',
                'is_featured' => true,
                'tags'        => ['studio ghibli', 'spirited away', 'poster', 'art print'],
            ],
            // Accessories
            [
                'name'        => 'Jujutsu Kaisen Gojo Satoru Acrylic Keychain',
                'category'    => 'Accessories',
                'description' => 'Double-sided 6cm acrylic keychain featuring Gojo Satoru. Comes with a silver-tone split ring. Perfect gift for JJK fans.',
                'price'       => 199.00,
                'compare_price' => null,
                'stock'       => 200,
                'sku'         => 'ACC-JJK-001',
                'is_featured' => false,
                'tags'        => ['jujutsu kaisen', 'gojo', 'keychain', 'accessory'],
            ],
            // Home
            [
                'name'        => 'Dragon Ball Z Capsule Corp Ceramic Mug',
                'category'    => 'Home & Lifestyle',
                'description' => '350ml ceramic mug with Capsule Corp logo print. Microwave and dishwasher safe. Comes in a gift box.',
                'price'       => 349.00,
                'compare_price' => 449.00,
                'stock'       => 60,
                'sku'         => 'HM-DBZ-001',
                'is_featured' => true,
                'tags'        => ['dragon ball', 'mug', 'capsule corp', 'home'],
            ],
        ];

        // Map category names to IDs
        $catMap = collect($createdCategories)->keyBy('name');

        foreach ($products as $p) {
            $category = $catMap[$p['category']] ?? $createdCategories[0];
            Product::create([
                'category_id'   => $category->id,
                'name'          => $p['name'],
                'slug'          => Str::slug($p['name']) . '-' . Str::random(4),
                'description'   => $p['description'],
                'price'         => $p['price'],
                'compare_price' => $p['compare_price'],
                'stock'         => $p['stock'],
                'sku'           => $p['sku'],
                'images'        => [],  // no actual images in seed — use placeholder in views
                'tags'          => $p['tags'],
                'is_active'     => true,
                'is_featured'   => $p['is_featured'],
            ]);
        }

        $this->command->info('✅ Seeded: 2 users, ' . count($categories) . ' categories, ' . count($products) . ' products.');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('email', 'seller@jajanku.id')->first();

        // Create categories
        $categories = [
            ['name' => 'Nasi & Lauk', 'slug' => 'nasi-lauk', 'icon' => '🍚'],
            ['name' => 'Mie & Bakso', 'slug' => 'mie-bakso', 'icon' => '🍜'],
            ['name' => 'Minuman',     'slug' => 'minuman',   'icon' => '🥤'],
            ['name' => 'Snack',       'slug' => 'snack',     'icon' => '🍿'],
            ['name' => 'Gorengan',    'slug' => 'gorengan',  'icon' => '🧆'],
            ['name' => 'Dessert',     'slug' => 'dessert',   'icon' => '🍡'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // Create sample shops
        $shops = [
            [
                'user_id'     => $seller->id,
                'name'        => 'Warung Bu Siti',
                'description' => 'Masakan rumahan yang lezat dan terjangkau',
                'address'     => 'Jl. Kampus No. 1, Depan Gedung A',
                'latitude'    => -6.9147,
                'longitude'   => 107.6098,
                'status'      => 'active',
            ],
        ];

        foreach ($shops as $shopData) {
            $shop = Shop::firstOrCreate(
                ['user_id' => $shopData['user_id']],
                $shopData
            );

            // Create products for this shop
            $this->seedProducts($shop);
        }

        // Create extra demo shops (different sellers)
        $this->createDemoShops();

        $this->command->info('Shops and products seeded!');
    }

    private function seedProducts(Shop $shop): void
    {
        $nasiLauk = Category::where('slug', 'nasi-lauk')->first();
        $minuman  = Category::where('slug', 'minuman')->first();
        $snack    = Category::where('slug', 'snack')->first();

        $products = [
            ['name' => 'Nasi Uduk + Lauk', 'price' => 12000, 'category_id' => $nasiLauk->id, 'is_available' => true, 'stock' => 20],
            ['name' => 'Nasi Goreng Spesial', 'price' => 15000, 'category_id' => $nasiLauk->id, 'is_available' => true, 'stock' => 15],
            ['name' => 'Ayam Goreng', 'price' => 10000, 'category_id' => $nasiLauk->id, 'is_available' => true, 'stock' => 10],
            ['name' => 'Es Teh Manis', 'price' => 4000, 'category_id' => $minuman->id, 'is_available' => true, 'stock' => 50],
            ['name' => 'Es Jeruk', 'price' => 5000, 'category_id' => $minuman->id, 'is_available' => true, 'stock' => 50],
            ['name' => 'Tempe Goreng (3pcs)', 'price' => 3000, 'category_id' => $snack->id, 'is_available' => true, 'stock' => 30],
            ['name' => 'Tahu Isi (3pcs)', 'price' => 3500, 'category_id' => $snack->id, 'is_available' => true, 'stock' => 30],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['shop_id' => $shop->id, 'name' => $productData['name']],
                array_merge($productData, ['shop_id' => $shop->id])
            );
        }
    }

    private function createDemoShops(): void
    {
        // Create additional demo seller users and shops
        $demoSellers = [
            [
                'email' => 'seller2@jajanku.id',
                'name'  => 'Warung Pak Ahmad',
                'shop'  => [
                    'name'        => 'Mie Ayam Pak Ahmad',
                    'description' => 'Mie ayam dan bakso segar setiap hari',
                    'address'     => 'Jl. Kampus No. 5, Kantin Belakang',
                    'latitude'    => -6.9150,
                    'longitude'   => 107.6110,
                    'status'      => 'active',
                ],
            ],
            [
                'email' => 'seller3@jajanku.id',
                'name'  => 'Kedai Kopi Nusantara',
                'shop'  => [
                    'name'        => 'Kedai Kopi Nusantara',
                    'description' => 'Kopi & minuman kekinian dengan cita rasa lokal',
                    'address'     => 'Jl. Universitas No. 3, Lobby Utama',
                    'latitude'    => -6.9140,
                    'longitude'   => 107.6095,
                    'status'      => 'active',
                ],
            ],
        ];

        $mie      = Category::where('slug', 'mie-bakso')->first();
        $minuman  = Category::where('slug', 'minuman')->first();

        foreach ($demoSellers as $sellerData) {
            $user = User::firstOrCreate(
                ['email' => $sellerData['email']],
                [
                    'name'     => $sellerData['name'],
                    'password' => bcrypt('password'),
                ]
            );
            $user->syncRoles(['seller']);

            $shop = Shop::firstOrCreate(
                ['user_id' => $user->id],
                array_merge($sellerData['shop'], ['user_id' => $user->id])
            );

            if ($mie && $minuman) {
                Product::firstOrCreate(
                    ['shop_id' => $shop->id, 'name' => 'Mie Ayam Original'],
                    ['shop_id' => $shop->id, 'name' => 'Mie Ayam Original', 'price' => 13000, 'category_id' => $mie->id, 'is_available' => true, 'stock' => 30]
                );
                Product::firstOrCreate(
                    ['shop_id' => $shop->id, 'name' => 'Bakso Campur'],
                    ['shop_id' => $shop->id, 'name' => 'Bakso Campur', 'price' => 15000, 'category_id' => $mie->id, 'is_available' => true, 'stock' => 20]
                );
                Product::firstOrCreate(
                    ['shop_id' => $shop->id, 'name' => 'Kopi Susu'],
                    ['shop_id' => $shop->id, 'name' => 'Kopi Susu', 'price' => 8000, 'category_id' => $minuman->id, 'is_available' => true, 'stock' => 50]
                );
            }
        }
    }
}

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
        $seller = User::where('email', 'pemilik@jajanku.id')->first();

        // ----------------------------------------------------------------
        // Categories
        // ----------------------------------------------------------------
        $categories = [
            ['name' => 'Gorengan',         'slug' => 'gorengan',         'icon' => '🍢'],
            ['name' => 'Bundling 1 Porsi', 'slug' => 'bundling-porsi',   'icon' => '🍜'],
            ['name' => 'Minuman',          'slug' => 'minuman',          'icon' => '🥤'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $gorengan = Category::where('slug', 'gorengan')->first();
        $bundling = Category::where('slug', 'bundling-porsi')->first();
        $minuman  = Category::where('slug', 'minuman')->first();

        // ----------------------------------------------------------------
        // Warung Bu Ipa - 1 warung tunggal
        // ----------------------------------------------------------------
        $shop = Shop::firstOrCreate(
            ['user_id' => $seller->id],
            [
                'user_id'     => $seller->id,
                'name'        => 'Warung Bu Ipa',
                'description' => 'Temukan jajanan SD mu disini! Gorengan, bundling porsi, dan minuman segar.',
                'address'     => 'Jl. Kampus No. 1, Area Kantin',
                'latitude'    => -6.9147,
                'longitude'   => 107.6098,
                'status'      => 'active',
            ]
        );

        // ----------------------------------------------------------------
        // Menu Gorengan
        // ----------------------------------------------------------------
        $gorenganMenu = [
            ['name' => 'Cireng Ayam',        'price' => 1000],
            ['name' => 'Cireng Ayam Suwir',  'price' => 1000],
            ['name' => 'Cilok',              'price' => 1000],
            ['name' => 'Risoles Ayam',       'price' => 1000],
            ['name' => 'Risol Mayo',         'price' => 1000],
            ['name' => 'Dimsum Goreng',      'price' => 1000],
            ['name' => 'Corndog',            'price' => 1000],
            ['name' => 'Otak-Otak',          'price' => 1000],
            ['name' => 'Sotong',             'price' => 1000],
            ['name' => 'Nuget',              'price' => 1000],
            ['name' => 'Sosis Kecil',        'price' => 1000],
            ['name' => 'Sosis Besar',        'price' => 3000],
            ['name' => 'Cikuwa',             'price' => 1000],
            ['name' => 'Sempol Ayam',        'price' => 1000],
            ['name' => 'Bakso Besar',        'price' => 2000],
            ['name' => 'Bakso Kecil',        'price' => 1000],
            ['name' => 'Dumpling Ayam',      'price' => 1000],
            ['name' => 'Dumpling Keju',      'price' => 1000],
            ['name' => 'Cakwe Mini',         'price' => 1000],
            ['name' => 'Fish Roll',          'price' => 1000],
            ['name' => 'Tofu',               'price' => 1000],
            ['name' => 'Wonton',             'price' => 1000],
            ['name' => 'Bakwan Sayur',       'price' => 1000],
            ['name' => 'Martabak Mini',      'price' => 1000],
        ];

        foreach ($gorenganMenu as $item) {
            Product::firstOrCreate(
                ['shop_id' => $shop->id, 'name' => $item['name']],
                [
                    'shop_id'      => $shop->id,
                    'category_id'  => $gorengan->id,
                    'price'        => $item['price'],
                    'description'  => $item['name'] . ' - jajanan favorit Bu Ipa',
                    'is_available' => true,
                    'stock'        => 50,
                ]
            );
        }

        // ----------------------------------------------------------------
        // Menu Bundling 1 Porsi
        // ----------------------------------------------------------------
        $bundlingMenu = [
            ['name' => 'Mie 1 Porsi',              'price' =>  3000],
            ['name' => 'Spaghetti',                'price' =>  3000],
            ['name' => 'Seafood Asam Manis',       'price' =>  3000],
            ['name' => 'Bakso Kuah Pedas',         'price' =>  3000],
            ['name' => 'Bubur Jagung',             'price' =>  3000],
            ['name' => 'Seblak',                   'price' =>  3000],
            ['name' => 'Tomyam',                   'price' =>  3000],
            ['name' => 'Mie Ayam',                 'price' =>  3000],
            ['name' => 'Otak-Otak Campur',         'price' =>  5000],
            ['name' => 'Otak-Otak Campur Jumbo',   'price' => 10000],
            ['name' => 'Sosis Besar Isi 2',        'price' =>  5000],
        ];

        foreach ($bundlingMenu as $item) {
            Product::firstOrCreate(
                ['shop_id' => $shop->id, 'name' => $item['name']],
                [
                    'shop_id'      => $shop->id,
                    'category_id'  => $bundling->id,
                    'price'        => $item['price'],
                    'description'  => $item['name'] . ' - bundling porsi spesial Bu Ipa',
                    'is_available' => true,
                    'stock'        => 20,
                ]
            );
        }

        // ----------------------------------------------------------------
        // Menu Minuman
        // ----------------------------------------------------------------
        $minumanMenu = [
            ['name' => 'Es Teh Manis',          'price' => 1000],
            ['name' => 'Es Teh Tarik / Bonteh', 'price' => 3000],
            ['name' => 'Es Bonteh Matcha',      'price' => 3000],
            ['name' => 'Serbuk Gula Batu',      'price' => 1000],
            ['name' => 'Serbuk Apel',           'price' => 1000],
            ['name' => 'Jasjus Anggur',         'price' => 1000],
            ['name' => 'Jasjus Jambu',          'price' => 1000],
            ['name' => 'Jasjus Mangga',         'price' => 1000],
            ['name' => 'Jasjus Jeruk Peras',    'price' => 1000],
            ['name' => 'Jasjus Melon',          'price' => 1000],
            ['name' => 'Cappucino',             'price' => 1000],
            ['name' => 'Pop Ice Stroberi',      'price' => 2000],
            ['name' => 'Pop Ice Alpukat',       'price' => 2000],
            ['name' => 'Pop Ice Taro',          'price' => 2000],
            ['name' => 'Pop Ice Cokelat',       'price' => 2000],
            ['name' => 'Pop Ice Anggur',        'price' => 2000],
            ['name' => 'Pop Ice Melon',         'price' => 2000],
            ['name' => 'Pop Ice Mangga',        'price' => 2000],
            ['name' => 'Pop Ice Permen Karet',  'price' => 2000],
            ['name' => 'Pop Ice Durian',        'price' => 2000],
            ['name' => 'Es Susu Cokelat',       'price' => 2000],
            ['name' => 'Es Susu Putih',         'price' => 2000],
            ['name' => 'Good Day',              'price' => 3000],
            ['name' => 'Nutrisari Jeruk Peras', 'price' => 2000],
            ['name' => 'Nutrisari Mangga',      'price' => 2000],
            ['name' => 'Nutrisari Sweet Orange','price' => 2000],
            ['name' => 'Es Cokelat Beng-Beng', 'price' => 2000],
            ['name' => 'Air Le Minerale',       'price' => 3000],
            ['name' => 'Air Aqua Viva',         'price' => 2000],
            ['name' => 'Cleo Kecil',            'price' => 1000],
            ['name' => 'Es Lilin',              'price' => 1000],
        ];

        foreach ($minumanMenu as $item) {
            Product::firstOrCreate(
                ['shop_id' => $shop->id, 'name' => $item['name']],
                [
                    'shop_id'      => $shop->id,
                    'category_id'  => $minuman->id,
                    'price'        => $item['price'],
                    'description'  => $item['name'] . ' - segar dan nikmat',
                    'is_available' => true,
                    'stock'        => 100,
                ]
            );
        }

        $total = count($gorenganMenu) + count($bundlingMenu) + count($minumanMenu);
        $this->command->info("✅ Warung Bu Ipa + {$total} produk berhasil di-seed!");
    }
}

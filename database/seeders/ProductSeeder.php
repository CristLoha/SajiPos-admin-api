<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $makanan = \App\Models\Category::where('name', 'Makanan Utama')->first();
        $minuman = \App\Models\Category::where('name', 'Minuman')->first();
        $snack = \App\Models\Category::where('name', 'Snack')->first();
        $kopi = \App\Models\Category::where('name', 'Kopi')->first();
        $teh = \App\Models\Category::where('name', 'Teh')->first();

        // If for some reason categories don't exist, fallback to first category
        $fallback = \App\Models\Category::first()->id ?? 1;

        $products = [
            // Makanan Utama
            ['category_id' => $makanan->id ?? $fallback, 'name' => 'Nasi Goreng Special', 'description' => 'Nasi goreng pedas gurih', 'price' => 25000, 'stock' => 50, 'status' => true],
            ['category_id' => $makanan->id ?? $fallback, 'name' => 'Ayam Bakar Madu', 'description' => 'Ayam bakar manis gurih', 'price' => 30000, 'stock' => 30, 'status' => true],
            ['category_id' => $makanan->id ?? $fallback, 'name' => 'Mie Goreng Seafood', 'description' => 'Mie goreng dengan udang', 'price' => 28000, 'stock' => 40, 'status' => true],
            
            // Snack
            ['category_id' => $snack->id ?? $fallback, 'name' => 'Kentang Goreng', 'description' => 'French fries renyah', 'price' => 15000, 'stock' => 100, 'status' => true],
            ['category_id' => $snack->id ?? $fallback, 'name' => 'Pisang Goreng Keju', 'description' => 'Pisang goreng manis', 'price' => 12000, 'stock' => 50, 'status' => true],

            // Minuman
            ['category_id' => $minuman->id ?? $fallback, 'name' => 'Jus Alpukat', 'description' => 'Jus alpukat kental', 'price' => 18000, 'stock' => 20, 'status' => true],
            ['category_id' => $minuman->id ?? $fallback, 'name' => 'Air Mineral', 'description' => 'Air mineral dingin', 'price' => 5000, 'stock' => 100, 'status' => true],

            // Kopi
            ['category_id' => $kopi->id ?? $fallback, 'name' => 'Kopi Susu Gula Aren', 'description' => 'Es kopi susu kekinian', 'price' => 20000, 'stock' => 60, 'status' => true],
            ['category_id' => $kopi->id ?? $fallback, 'name' => 'Americano Dingin', 'description' => 'Kopi hitam dingin', 'price' => 18000, 'stock' => 50, 'status' => true],

            // Teh
            ['category_id' => $teh->id ?? $fallback, 'name' => 'Es Teh Manis', 'description' => 'Es teh manis segar', 'price' => 8000, 'stock' => 200, 'status' => true],
            ['category_id' => $teh->id ?? $fallback, 'name' => 'Thai Tea', 'description' => 'Teh khas Thailand', 'price' => 15000, 'stock' => 50, 'status' => true],
            ['category_id' => $teh->id ?? $fallback, 'name' => 'Lemon Tea Panas', 'description' => 'Teh lemon hangat', 'price' => 10000, 'stock' => 50, 'status' => true],
        ];

        foreach ($products as $prod) {
            \App\Models\Product::create($prod);
        }
    }
}

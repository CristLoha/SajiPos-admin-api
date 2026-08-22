<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Utama', 'description' => 'Berbagai macam makanan berat dan nasi'],
            ['name' => 'Minuman', 'description' => 'Aneka minuman dingin dan hangat'],
            ['name' => 'Snack', 'description' => 'Makanan ringan dan camilan'],
            ['name' => 'Kopi', 'description' => 'Aneka olahan kopi'],
            ['name' => 'Teh', 'description' => 'Aneka minuman teh segar'],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::create($cat);
        }
    }
}

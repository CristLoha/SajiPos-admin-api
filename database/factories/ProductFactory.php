<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
        $foodNames = [
            'Nasi Goreng Special', 'Mie Goreng Seafood', 'Ayam Bakar Madu', 'Sate Ayam Madura',
            'Es Teh Manis', 'Jus Alpukat', 'Kopi Susu Gula Aren', 'Thai Tea',
            'Kentang Goreng', 'Roti Bakar Cokelat', 'Pisang Goreng Keju',
            'Ice Cream Vanilla', 'Waffle Caramel', 'Salad Buah Segar'
        ];

        return [
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id ?? \App\Models\Category::factory(),
            'name' => $this->faker->randomElement($foodNames) . ' ' . $this->faker->unique()->numberBetween(1, 100),
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->randomElement([15000, 18000, 20000, 25000, 28000, 30000, 35000, 45000]),
            'stock' => $this->faker->numberBetween(10, 100),
            'image' => null,
            'status' => $this->faker->boolean(90), // 90% chance of being active/ready
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Makanan Utama', 'Minuman Dingin', 'Minuman Hangat', 'Camilan / Snack', 'Pencuci Mulut / Dessert', 'Paket Hemat', 'Kopi', 'Teh']),
            'description' => $this->faker->sentence(),
            'image' => null,
        ];
    }
}

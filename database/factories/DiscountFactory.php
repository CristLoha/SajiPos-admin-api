<?php

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['percentage', 'fixed']);
        return [
            'name' => $this->faker->randomElement(['Diskon Member Baru', 'Promo Gajian', 'Diskon Hari Kemerdekaan', 'Promo Kilat Makan Siang', 'Weekend Deal']),
            'description' => $this->faker->sentence(),
            'type' => $type,
            'value' => $type === 'percentage' ? $this->faker->randomElement([10, 15, 20, 25]) : $this->faker->randomElement([5000, 10000, 15000, 20000]),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'expired_date' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
        ];
    }
}

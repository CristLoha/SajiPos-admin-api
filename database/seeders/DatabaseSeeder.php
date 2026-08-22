<?php

namespace Database\Seeders;

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
        // Admin user
        User::factory()->create([
            'name' => 'Admin SajiPOS',
            'email' => 'admin@sajipos.com',
            'username' => 'admin',
            'password' => bcrypt('password123'),
            'roles' => 'admin',
        ]);

        // Staff user
        User::factory()->create([
            'name' => 'Staff SajiPOS',
            'email' => 'staff@sajipos.com',
            'username' => 'staff',
            'password' => bcrypt('password123'),
            'roles' => 'staff',
        ]);

        // User (Kasir)
        User::factory()->create([
            'name' => 'Kasir SajiPOS',
            'email' => 'kasir@sajipos.com',
            'username' => 'kasir',
            'password' => bcrypt('password123'),
            'roles' => 'user',
        ]);

        // 15 dummy users
        User::factory(15)->create();

        // Seed Categories
        $this->call(CategorySeeder::class);

        // Seed Products
        $this->call(ProductSeeder::class);

        // Seed Discounts
        $this->call(DiscountSeeder::class);

        // Seed Orders
        $this->call(OrderSeeder::class);
    }
}

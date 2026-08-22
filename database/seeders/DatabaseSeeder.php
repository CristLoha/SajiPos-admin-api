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
        // ==========================================
        // AKUN ADMIN UTAMA (Ganti password setelah login!)
        // ==========================================
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@sajipos.com',
            'username' => 'admin',
            'password' => bcrypt('admin12345'), // Pastikan diubah nanti!
            'roles' => 'admin',
        ]);

        // Akun Staff (Kasir)
        User::factory()->create([
            'name' => 'Kasir Utama',
            'email' => 'kasir@sajipos.com',
            'username' => 'kasir',
            'password' => bcrypt('kasir12345'),
            'roles' => 'staff',
        ]);

        // HAPUS pembuatan 15 user dummy karena sangat berbahaya di production 
        // (passwordnya seragam dan bisa ditebak orang)

        // Seed Master Data
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(DiscountSeeder::class);
        
        // HAPUS OrderSeeder agar data laporan kosong saat pertama kali rilis
        // $this->call(OrderSeeder::class);
    }
}

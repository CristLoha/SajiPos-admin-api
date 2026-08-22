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

        // Hapus akun staff/kasir dan data dummy lainnya.
        // Semua data master (Kategori, Produk, Diskon, Akun Staff) 
        // silakan ditambahkan secara manual oleh Admin melalui Dashboard.
    }
}

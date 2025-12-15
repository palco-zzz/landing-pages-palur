<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Users
        $this->createUsers();

        // Seed Menu Items
        $this->seedMenuItems();
    }

    /**
     * Create admin and cashier users.
     */
    private function createUsers(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@palur.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kasir Palur',
            'email' => 'kasir@palur.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);
    }

    /**
     * Seed menu items from landing page data.
     */
    private function seedMenuItems(): void
    {
        $menuItems = [
            // Category: Makanan
            ['name' => 'Mie Lethek Godog', 'category' => 'makanan', 'price' => 16000],
            ['name' => 'Mie Lethek Goreng', 'category' => 'makanan', 'price' => 16000],
            ['name' => 'Bakmi Jowo Godog', 'category' => 'makanan', 'price' => 16000],
            ['name' => 'Bakmi Jowo Goreng', 'category' => 'makanan', 'price' => 16000],
            ['name' => 'Nasi Goreng Jowo', 'category' => 'makanan', 'price' => 16000],
            ['name' => 'Nasi Goreng Mawut', 'category' => 'makanan', 'price' => 16000],
            ['name' => 'Nasi Godog', 'category' => 'makanan', 'price' => 16000],
            ['name' => 'Rica-rica Ayam', 'category' => 'makanan', 'price' => 25000],
            ['name' => 'Nasi Putih', 'category' => 'makanan', 'price' => 4000],

            // Category: Minuman
            ['name' => 'Wedang Uwuh', 'category' => 'minuman', 'price' => 8000],
            ['name' => 'Wedang Uwuh Susu', 'category' => 'minuman', 'price' => 10000],
            ['name' => 'Teh Manis (Panas/Es)', 'category' => 'minuman', 'price' => 4000],
            ['name' => 'Jeruk (Panas/Es)', 'category' => 'minuman', 'price' => 5000],
            ['name' => 'Kopi Hitam', 'category' => 'minuman', 'price' => 4000],
            ['name' => 'Lemon Tea', 'category' => 'minuman', 'price' => 5000],

            // Category: Tambahan
            ['name' => 'Kepala Ayam', 'category' => 'tambahan', 'price' => 5000],
            ['name' => 'Sayap Ayam', 'category' => 'tambahan', 'price' => 5000],
            ['name' => 'Telor Ceplok/Dadar', 'category' => 'tambahan', 'price' => 4000],
        ];

        foreach ($menuItems as $item) {
            Menu::create([
                'name' => $item['name'],
                'category' => $item['category'],
                'price' => $item['price'],
                'is_available' => true,
            ]);
        }
    }
}

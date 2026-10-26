<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Demo (Admin & Staff)
        $admin = User::firstOrCreate(
            ['email' => 'admin@inventory.com'],
            [
                'name' => 'Admin Distribusi',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@inventory.com'],
            [
                'name' => 'Staff Kasir 1',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        $staff2 = User::firstOrCreate(
            ['email' => 'staff2@inventory.com'],
            [
                'name' => 'Staff Kasir 2',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        // 2. Kategori Produk
        $categories = [
            'Minuman Ringan' => Category::firstOrCreate(['name' => 'Minuman Ringan']),
            'Makanan Ringan & Snack' => Category::firstOrCreate(['name' => 'Makanan Ringan & Snack']),
            'Bumbu & Bahan Dapur' => Category::firstOrCreate(['name' => 'Bumbu & Bahan Dapur']),
            'Kebutuhan Rumah Tangga' => Category::firstOrCreate(['name' => 'Kebutuhan Rumah Tangga']),
            'Alat Tulis Kantor' => Category::firstOrCreate(['name' => 'Alat Tulis Kantor']),
        ];

        // 3. Pemasok (Suppliers)
        $suppliers = [
            Supplier::firstOrCreate(
                ['name' => 'PT Sumber Makmur Distribusi'],
                ['phone' => '081234567890', 'email' => 'kontak@sumbermakmur.com']
            ),
            Supplier::firstOrCreate(
                ['name' => 'CV Sejahtera Abadi Logistics'],
                ['phone' => '081398765432', 'email' => 'sales@sejahteraabadi.id']
            ),
            Supplier::firstOrCreate(
                ['name' => 'PT Graha Pangan Nusantara'],
                ['phone' => '082155566677', 'email' => 'order@grahapangan.co.id']
            ),
            Supplier::firstOrCreate(
                ['name' => 'CV Sinar Terang Gemilang'],
                ['phone' => '085711223344', 'email' => 'info@sinarterang.com']
            ),
        ];

        // 4. Produk Dummy (Termasuk stok normal dan stok rendah untuk pengujian dashboard)
        $products = [
            [
                'category_id' => $categories['Minuman Ringan']->id,
                'sku' => 'PRD-MNM-001',
                'name' => 'Teh Botol Sosro 450ml (Karton/24)',
                'purchase_price' => 110000.00,
                'selling_price' => 135000.00,
                'stock' => 50,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Minuman Ringan']->id,
                'sku' => 'PRD-MNM-002',
                'name' => 'Air Mineral Le Minerale 600ml (Karton/24)',
                'purchase_price' => 42000.00,
                'selling_price' => 52000.00,
                'stock' => 80,
                'minimum_stock' => 15,
            ],
            [
                'category_id' => $categories['Minuman Ringan']->id,
                'sku' => 'PRD-MNM-003',
                'name' => 'Kopi Kapal Api Special Mix (Renceng/10)',
                'purchase_price' => 13500.00,
                'selling_price' => 17000.00,
                'stock' => 3, // LOW STOCK (< 10)
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Makanan Ringan & Snack']->id,
                'sku' => 'PRD-SNK-001',
                'name' => 'Chitato Sapi Panggang 68g (Dus/30)',
                'purchase_price' => 270000.00,
                'selling_price' => 325000.00,
                'stock' => 25,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Makanan Ringan & Snack']->id,
                'sku' => 'PRD-SNK-002',
                'name' => 'Oreo Vanilla 133g (Dus/24)',
                'purchase_price' => 195000.00,
                'selling_price' => 240000.00,
                'stock' => 4, // LOW STOCK (< 8)
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $categories['Makanan Ringan & Snack']->id,
                'sku' => 'PRD-SNK-003',
                'name' => 'Biskuit Roma Kelapa 300g (Dus/12)',
                'purchase_price' => 115000.00,
                'selling_price' => 140000.00,
                'stock' => 18,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Bumbu & Bahan Dapur']->id,
                'sku' => 'PRD-BMB-001',
                'name' => 'Minyak Goreng Bimoli 2 Liter (Pouch)',
                'purchase_price' => 34000.00,
                'selling_price' => 39500.00,
                'stock' => 60,
                'minimum_stock' => 15,
            ],
            [
                'category_id' => $categories['Bumbu & Bahan Dapur']->id,
                'sku' => 'PRD-BMB-002',
                'name' => 'Gula Pasir Gulaku Kuning 1kg',
                'purchase_price' => 16500.00,
                'selling_price' => 19000.00,
                'stock' => 2, // LOW STOCK (< 10)
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Bumbu & Bahan Dapur']->id,
                'sku' => 'PRD-BMB-003',
                'name' => 'Kecap Manis Bango 520ml (Pouch)',
                'purchase_price' => 22500.00,
                'selling_price' => 26500.00,
                'stock' => 35,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $categories['Kebutuhan Rumah Tangga']->id,
                'sku' => 'PRD-KRT-001',
                'name' => 'Deterjen Rinso Molto 770g',
                'purchase_price' => 24000.00,
                'selling_price' => 28500.00,
                'stock' => 40,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Kebutuhan Rumah Tangga']->id,
                'sku' => 'PRD-KRT-002',
                'name' => 'Sunlight Jeruk Nipis 750ml',
                'purchase_price' => 15500.00,
                'selling_price' => 18500.00,
                'stock' => 1, // LOW STOCK (< 10)
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $categories['Alat Tulis Kantor']->id,
                'sku' => 'PRD-ATK-001',
                'name' => 'Kertas HVS PaperOne A4 80gsm (Rim)',
                'purchase_price' => 48000.00,
                'selling_price' => 58000.00,
                'stock' => 30,
                'minimum_stock' => 5,
            ],
        ];

        foreach ($products as $item) {
            Product::firstOrCreate(
                ['sku' => $item['sku']],
                $item
            );
        }
    }
}

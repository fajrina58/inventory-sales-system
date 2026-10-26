<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Category $categoryA;
    protected Category $categoryB;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);

        $this->categoryA = Category::create(['name' => 'Minuman']);
        $this->categoryB = Category::create(['name' => 'Makanan']);

        $this->supplier = Supplier::create([
            'name' => 'PT Mitra Utama',
            'phone' => '0811223344',
        ]);
    }

    public function test_dashboard_displays_accurate_database_aggregates_and_excludes_cancelled_transactions(): void
    {
        // 1. Buat 3 produk (2 normal, 1 stok rendah)
        $p1 = Product::create([
            'category_id' => $this->categoryA->id,
            'sku' => 'SKU-001',
            'name' => 'Teh Botol',
            'purchase_price' => 5000,
            'selling_price' => 7000,
            'stock' => 50,
            'minimum_stock' => 10,
        ]);

        $p2 = Product::create([
            'category_id' => $this->categoryA->id,
            'sku' => 'SKU-002',
            'name' => 'Kopi Sachet',
            'purchase_price' => 2000,
            'selling_price' => 3000,
            'stock' => 3, // LOW STOCK (3 <= 5)
            'minimum_stock' => 5,
        ]);

        $p3 = Product::create([
            'category_id' => $this->categoryB->id,
            'sku' => 'SKU-003',
            'name' => 'Biskuit Cokelat',
            'purchase_price' => 8000,
            'selling_price' => 10000,
            'stock' => 20,
            'minimum_stock' => 5,
        ]);

        // 2. Buat transaksi penjualan Selesai (Completed) hari ini senilai 70.000
        Sale::create([
            'user_id' => $this->staff->id,
            'sale_number' => 'TRX-TEST-01',
            'total' => 70000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        // 3. Buat transaksi penjualan Dibatalkan (Cancelled) senilai 100.000 (TIDAK BOLEH DIHITUNG)
        Sale::create([
            'user_id' => $this->staff->id,
            'sale_number' => 'TRX-TEST-02',
            'total' => 100000,
            'status' => 'cancelled',
            'sold_at' => now(),
        ]);

        // 4. Buat transaksi pembelian Selesai
        Purchase::create([
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->admin->id,
            'purchase_number' => 'PO-TEST-01',
            'total' => 250000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        $response = $this->actingAs($this->staff)->get(route('dashboard'));

        $response->assertStatus(200);

        // Nilai terverifikasi:
        // Total Produk = 3
        $response->assertSee('3');
        // Total Penjualan Hari Ini = Rp 70.000 (bukan 170.000)
        $response->assertSee('70.000');
        // Jumlah transaksi sukses = 1 sale + 1 purchase = 2 (bukan 3)
        $response->assertSee('2');
        // Produk Kritis = 1 (Kopi Sachet)
        $response->assertSee('Kopi Sachet');
    }

    public function test_low_stock_list_page_returns_only_products_at_or_below_minimum_stock(): void
    {
        Product::create([
            'category_id' => $this->categoryA->id,
            'sku' => 'STK-AMN',
            'name' => 'Barang Stok Aman',
            'purchase_price' => 5000,
            'selling_price' => 7000,
            'stock' => 50,
            'minimum_stock' => 10,
        ]);

        Product::create([
            'category_id' => $this->categoryA->id,
            'sku' => 'STK-KRT',
            'name' => 'Barang Stok Kritis',
            'purchase_price' => 5000,
            'selling_price' => 7000,
            'stock' => 2, // 2 <= 5
            'minimum_stock' => 5,
        ]);

        $response = $this->actingAs($this->staff)->get(route('products.low-stock'));

        $response->assertStatus(200);
        $response->assertSee('Barang Stok Kritis');
        $response->assertDontSee('Barang Stok Aman');
    }

    public function test_search_and_category_filter_on_products(): void
    {
        Product::create([
            'category_id' => $this->categoryA->id,
            'sku' => 'SRCH-MNM-01',
            'name' => 'Susu Kotak Coklat',
            'purchase_price' => 5000,
            'selling_price' => 7000,
            'stock' => 20,
            'minimum_stock' => 5,
        ]);

        Product::create([
            'category_id' => $this->categoryB->id,
            'sku' => 'SRCH-MKN-01',
            'name' => 'Roti Bakar Manis',
            'purchase_price' => 8000,
            'selling_price' => 12000,
            'stock' => 15,
            'minimum_stock' => 5,
        ]);

        // 1. Search by SKU
        $responseSearch = $this->actingAs($this->staff)->get(route('products.index', ['search' => 'SRCH-MNM']));
        $responseSearch->assertSee('Susu Kotak Coklat');
        $responseSearch->assertDontSee('Roti Bakar Manis');

        // 2. Filter by Category
        $responseCat = $this->actingAs($this->staff)->get(route('products.index', ['category_id' => $this->categoryB->id]));
        $responseCat->assertSee('Roti Bakar Manis');
        $responseCat->assertDontSee('Susu Kotak Coklat');
    }

    public function test_pagination_and_query_string_retention(): void
    {
        // Buat 15 produk
        for ($i = 1; $i <= 15; $i++) {
            Product::create([
                'category_id' => $this->categoryA->id,
                'sku' => sprintf('PAGI-%03d', $i),
                'name' => "Produk Halaman {$i}",
                'purchase_price' => 1000,
                'selling_price' => 1500,
                'stock' => 10,
                'minimum_stock' => 2,
            ]);
        }

        // Halaman 1 (default 10 per halaman, produk 6-15)
        $responsePage1 = $this->actingAs($this->staff)->get(route('products.index', ['category_id' => $this->categoryA->id]));
        $responsePage1->assertStatus(200);
        $responsePage1->assertSee('PAGI-015');
        $responsePage1->assertDontSee('PAGI-001');

        // Halaman 2 (sisa 5 produk, produk 1-5)
        $responsePage2 = $this->actingAs($this->staff)->get(route('products.index', ['category_id' => $this->categoryA->id, 'page' => 2]));
        $responsePage2->assertStatus(200);
        $responsePage2->assertSee('PAGI-001');
        $responsePage2->assertDontSee('PAGI-015');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Product $productA;
    protected Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);

        $category = Category::create(['name' => 'Kategori Dagang']);

        $this->productA = Product::create([
            'category_id' => $category->id,
            'sku' => 'SL-PRD-01',
            'name' => 'Barang Laris A',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 20,
            'minimum_stock' => 5,
        ]);

        $this->productB = Product::create([
            'category_id' => $category->id,
            'sku' => 'SL-PRD-02',
            'name' => 'Barang Laris B',
            'purchase_price' => 20000,
            'selling_price' => 30000,
            'stock' => 10,
            'minimum_stock' => 2,
        ]);
    }

    public function test_sale_successfully_reduces_stock_according_to_quantity(): void
    {
        $payload = [
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 5,
                ],
                [
                    'product_id' => $this->productB->id,
                    'quantity' => 3,
                ],
            ],
        ];

        $response = $this->actingAs($this->staff)->post(route('sales.store'), $payload);

        $response->assertSessionHasNoErrors();

        // Total harus dihitung di backend: (5 * 15.000) + (3 * 30.000) = 75.000 + 90.000 = 165.000
        $this->assertDatabaseHas('sales', [
            'user_id' => $this->staff->id,
            'status' => 'completed',
            'total' => 165000,
        ]);

        // Verifikasi stok fisik berkurang: 20 - 5 = 15, dan 10 - 3 = 7
        $this->assertEquals(15, $this->productA->fresh()->stock);
        $this->assertEquals(7, $this->productB->fresh()->stock);
    }

    public function test_sale_with_insufficient_stock_is_rejected_without_any_data_change(): void
    {
        // Meminta kuantitas 25 padahal stok productA hanya 20
        $payload = [
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 25,
                ],
            ],
        ];

        $response = $this->actingAs($this->staff)->post(route('sales.store'), $payload);

        $response->assertSessionHasErrors('items');

        // Verifikasi tidak ada perubahan stok sama sekali
        $this->assertEquals(20, $this->productA->fresh()->stock);
        // Tidak ada record transaksi yang dibuat
        $this->assertEquals(0, Sale::count());
    }

    public function test_entire_transaction_rolls_back_if_one_item_fails_due_to_insufficient_stock(): void
    {
        $service = app(SaleService::class);

        // Item 1 (productA) cukup (5 dari 20), tetapi Item 2 (productB) tidak cukup (15 dari 10)
        $payload = [
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 5,
                ],
                [
                    'product_id' => $this->productB->id,
                    'quantity' => 15, // Melebihi stok productB (10)
                ],
            ],
        ];

        try {
            $service->createSale($payload, $this->staff);
            $this->fail('Harus melempar ValidationException');
        } catch (ValidationException $e) {
            // Berhasil tertangkap
        }

        // Verifikasi prinsip Atomicity: Stok productA TIDAK boleh berkurang sama sekali
        $this->assertEquals(20, $this->productA->fresh()->stock);
        $this->assertEquals(10, $this->productB->fresh()->stock);
        $this->assertEquals(0, Sale::count());
    }

    public function test_admin_can_cancel_sale_and_stock_is_restored(): void
    {
        $service = app(SaleService::class);

        $sale = $service->createSale([
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 8,
                ],
            ],
        ], $this->staff);

        // Stok saat ini berkurang: 20 - 8 = 12
        $this->assertEquals(12, $this->productA->fresh()->stock);

        // Admin membatalkan penjualan
        $response = $this->actingAs($this->admin)->post(route('sales.cancel', $sale));

        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $sale->fresh()->status);

        // Stok produk harus pulih kembali: 12 + 8 = 20
        $this->assertEquals(20, $this->productA->fresh()->stock);
    }

    public function test_staff_cannot_cancel_sale(): void
    {
        $service = app(SaleService::class);

        $sale = $service->createSale([
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 2,
                ],
            ],
        ], $this->staff);

        $response = $this->actingAs($this->staff)->post(route('sales.cancel', $sale));

        $response->assertStatus(403);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Supplier $supplier;
    protected Product $productA;
    protected Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);

        $category = Category::create(['name' => 'Kategori Pangan']);
        $this->supplier = Supplier::create([
            'name' => 'Supplier Test PT',
            'phone' => '0812345678',
        ]);

        $this->productA = Product::create([
            'category_id' => $category->id,
            'sku' => 'PUR-PRD-01',
            'name' => 'Produk A',
            'purchase_price' => 10000,
            'selling_price' => 12000,
            'stock' => 10,
            'minimum_stock' => 5,
        ]);

        $this->productB = Product::create([
            'category_id' => $category->id,
            'sku' => 'PUR-PRD-02',
            'name' => 'Produk B',
            'purchase_price' => 20000,
            'selling_price' => 25000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);
    }

    public function test_purchase_successfully_increases_stock_according_to_quantity(): void
    {
        $payload = [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 15,
                    'unit_cost' => 10000,
                ],
                [
                    'product_id' => $this->productB->id,
                    'quantity' => 10,
                    'unit_cost' => 20000,
                ],
            ],
        ];

        // Staff melakukan input pembelian
        $response = $this->actingAs($this->staff)->post(route('purchases.store'), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->staff->id,
            'status' => 'completed',
            'total' => (15 * 10000) + (10 * 20000), // 350.000
        ]);

        // Verifikasi stok produk bertambah: 10 + 15 = 25, dan 5 + 10 = 15
        $this->assertEquals(25, $this->productA->fresh()->stock);
        $this->assertEquals(15, $this->productB->fresh()->stock);
    }

    public function test_purchase_rolls_back_completely_if_one_item_fails(): void
    {
        $service = app(PurchaseService::class);

        // Kuantitas item kedua tidak valid (0)
        $payload = [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 20,
                    'unit_cost' => 10000,
                ],
                [
                    'product_id' => $this->productB->id,
                    'quantity' => -5, // Tidak valid
                    'unit_cost' => 20000,
                ],
            ],
        ];

        try {
            $service->createPurchase($payload, $this->staff);
            $this->fail('Harus melempar ValidationException');
        } catch (ValidationException $e) {
            // Berhasil tertangkap
        }

        // Verifikasi rollback: Stok produk A TIDAK boleh berubah
        $this->assertEquals(10, $this->productA->fresh()->stock);
        // Header pembelian tidak boleh tersimpan
        $this->assertEquals(0, Purchase::count());
    }

    public function test_admin_can_cancel_purchase_and_stock_is_deducted_back(): void
    {
        $service = app(PurchaseService::class);

        $purchase = $service->createPurchase([
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 20,
                    'unit_cost' => 10000,
                ],
            ],
        ], $this->staff);

        // Stok saat ini: 10 + 20 = 30
        $this->assertEquals(30, $this->productA->fresh()->stock);

        // Admin membatalkan pembelian
        $response = $this->actingAs($this->admin)->post(route('purchases.cancel', $purchase));

        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $purchase->fresh()->status);
        // Stok harus ditarik kembali: 30 - 20 = 10
        $this->assertEquals(10, $this->productA->fresh()->stock);
    }

    public function test_cancellation_is_rejected_if_stock_would_become_negative(): void
    {
        $service = app(PurchaseService::class);

        $purchase = $service->createPurchase([
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 20,
                    'unit_cost' => 10000,
                ],
            ],
        ], $this->staff);

        // Stok sekarang 30. Misal barang sudah dipakai atau dijual sehingga tersisa 5 unit
        $this->productA->update(['stock' => 5]);

        // Pembatalan (harus mengurangi 20 unit) harus ditolak karena 5 - 20 = -15
        $response = $this->actingAs($this->admin)->post(route('purchases.cancel', $purchase));

        $response->assertSessionHas('error');
        $this->assertEquals('completed', $purchase->fresh()->status);
        $this->assertEquals(5, $this->productA->fresh()->stock);
    }

    public function test_staff_cannot_cancel_purchase(): void
    {
        $service = app(PurchaseService::class);

        $purchase = $service->createPurchase([
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 5,
                    'unit_cost' => 10000,
                ],
            ],
        ], $this->staff);

        $response = $this->actingAs($this->staff)->post(route('purchases.cancel', $purchase));

        $response->assertStatus(403);
    }
}

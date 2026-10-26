<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);
        $this->category = Category::create(['name' => 'Kategori Uji']);
    }

    public function test_staff_can_view_product_list(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'sku' => 'TEST-001',
            'name' => 'Barang Uji 1',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 20,
            'minimum_stock' => 5,
        ]);

        $response = $this->actingAs($this->staff)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Barang Uji 1');
    }

    public function test_staff_cannot_create_product_and_receives_403(): void
    {
        $response = $this->actingAs($this->staff)->get(route('products.create'));

        $response->assertStatus(403);
    }

    public function test_staff_cannot_store_product_and_receives_403(): void
    {
        $payload = [
            'category_id' => $this->category->id,
            'sku' => 'TEST-STF',
            'name' => 'Produk Ilegal',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 10,
            'minimum_stock' => 2,
        ];

        $response = $this->actingAs($this->staff)->post(route('products.store'), $payload);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('products', ['sku' => 'TEST-STF']);
    }

    public function test_admin_can_create_product_with_valid_data(): void
    {
        $payload = [
            'category_id' => $this->category->id,
            'sku' => 'PRD-ADM-001',
            'name' => 'Produk Sah Admin',
            'purchase_price' => 50000,
            'selling_price' => 65000,
            'stock' => 100,
            'minimum_stock' => 10,
        ];

        $response = $this->actingAs($this->admin)->post(route('products.store'), $payload);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['sku' => 'PRD-ADM-001']);
    }

    public function test_duplicate_sku_is_rejected_by_validation(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'sku' => 'DUPLICATE-SKU',
            'name' => 'Barang Pertama',
            'purchase_price' => 10000,
            'selling_price' => 12000,
            'stock' => 10,
            'minimum_stock' => 2,
        ]);

        $payload = [
            'category_id' => $this->category->id,
            'sku' => 'DUPLICATE-SKU',
            'name' => 'Barang Kedua (Duplikat)',
            'purchase_price' => 20000,
            'selling_price' => 25000,
            'stock' => 5,
            'minimum_stock' => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('products.store'), $payload);

        $response->assertSessionHasErrors('sku');
    }

    public function test_negative_price_or_stock_is_rejected(): void
    {
        $payload = [
            'category_id' => $this->category->id,
            'sku' => 'NEG-PRD',
            'name' => 'Produk Negatif',
            'purchase_price' => -1000,
            'selling_price' => -500,
            'stock' => -10,
            'minimum_stock' => -1,
        ];

        $response = $this->actingAs($this->admin)->post(route('products.store'), $payload);

        $response->assertSessionHasErrors(['purchase_price', 'selling_price', 'stock', 'minimum_stock']);
    }

    public function test_category_cannot_be_deleted_if_has_products(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'sku' => 'BOUND-PRD',
            'name' => 'Produk Terikat Kategori',
            'purchase_price' => 10000,
            'selling_price' => 12000,
            'stock' => 10,
            'minimum_stock' => 2,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('categories.destroy', $this->category));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Kolom yang dapat diisi secara massal (mass-assignment protection).
     */
    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'purchase_price',
        'selling_price',
        'stock',
        'minimum_stock',
    ];

    /**
     * Casting tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock' => 'integer',
            'minimum_stock' => 'integer',
        ];
    }

    /**
     * Relasi ke kategori induk produk.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke riwayat item pembelian barang masuk.
     */
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Relasi ke riwayat item penjualan barang keluar.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Scope untuk menyaring produk dengan stok di bawah atau sama dengan batas minimum stok.
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'minimum_stock');
    }

    /**
     * Helper untuk mengecek apakah stok produk sudah menipis/kritis.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }

    /**
     * Helper untuk mengecek apakah produk sudah pernah digunakan dalam transaksi.
     */
    public function hasTransactions(): bool
    {
        return $this->purchaseItems()->exists() || $this->saleItems()->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    /**
     * Kolom mass-assignable.
     */
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    /**
     * Casting tipe data.
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke transaksi penjualan induk.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relasi ke produk (termasuk jika produk di-soft-delete).
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}

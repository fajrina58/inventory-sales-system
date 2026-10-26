<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    /**
     * Kolom mass-assignable.
     */
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_cost',
        'subtotal',
    ];

    /**
     * Casting tipe data.
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke transaksi pembelian induk.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Relasi ke produk (termasuk jika produk di-soft-delete).
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}

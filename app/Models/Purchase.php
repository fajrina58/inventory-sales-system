<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    /**
     * Kolom mass-assignable.
     */
    protected $fillable = [
        'supplier_id',
        'user_id',
        'purchase_number',
        'total',
        'status',
        'purchased_at',
    ];

    /**
     * Casting tipe data.
     */
    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'purchased_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke supplier penyedia barang.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke user pembuat transaksi pembelian.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke detail item pembelian.
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Cek apakah status transaksi completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Cek apakah status transaksi cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    /**
     * Kolom mass-assignable.
     */
    protected $fillable = [
        'user_id',
        'sale_number',
        'total',
        'status',
        'sold_at',
    ];

    /**
     * Casting tipe data.
     */
    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'sold_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke kasir/staf yang menangani penjualan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke detail item penjualan.
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * Mass assignment protection.
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
    ];

    /**
     * Relasi ke transaksi pembelian dari supplier ini.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Mass assignment protection.
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Relasi ke produk yang berkategori ini.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

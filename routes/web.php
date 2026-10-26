<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master Data - Akses Baca (Admin & Staff)
    Route::get('products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('products/low-stock', [\App\Http\Controllers\ProductController::class, 'lowStock'])->name('products.low-stock');
    Route::get('suppliers', [\App\Http\Controllers\SupplierController::class, 'index'])->name('suppliers.index');
});

// Master Data & User Management - Akses Kelola Khusus Administrator
Route::middleware(['auth', 'admin'])->group(function () {
    // Categories CRUD (Khusus Admin)
    Route::resource('categories', \App\Http\Controllers\CategoryController::class)->except(['show']);

    // Suppliers Modify (Khusus Admin)
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->except(['index', 'show']);

    // Products Create & Modify (Khusus Admin, didefinisikan sebelum wildcard {product})
    Route::resource('products', \App\Http\Controllers\ProductController::class)->except(['index', 'show']);

    // User & Role Management (Khusus Admin)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
    });
});

// Rute detail produk (bisa diakses setelah rute create/edit terdaftar)
Route::middleware('auth')->group(function () {
    Route::get('products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

    // Transaksi Pembelian (Stock-In) - Staff & Admin
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class)->only(['index', 'create', 'store', 'show']);

    // Transaksi Penjualan (Stock-Out) - Staff & Admin (Tidak ada rute edit/update untuk transaksi selesai)
    Route::resource('sales', \App\Http\Controllers\SaleController::class)->only(['index', 'create', 'store', 'show']);
});

// Pembatalan Transaksi (Khusus Admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('purchases/{purchase}/cancel', [\App\Http\Controllers\PurchaseController::class, 'cancel'])->name('purchases.cancel');
    Route::post('sales/{sale}/cancel', [\App\Http\Controllers\SaleController::class, 'cancel'])->name('sales.cancel');
});

require __DIR__.'/auth.php';

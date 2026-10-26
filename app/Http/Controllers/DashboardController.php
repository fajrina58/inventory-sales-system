<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan ringkasan metrik inventori dan transaksi dengan query agregat basis data riil.
     */
    public function index()
    {
        // 1. Total Produk Aktif (Query Agregat Count)
        $totalProducts = Product::count();

        // 2. Jumlah Transaksi Sukses / Selesai (Completed)
        $completedSalesCount = Sale::where('status', 'completed')->count();
        $completedPurchasesCount = Purchase::where('status', 'completed')->count();
        $totalTransactionsCount = $completedSalesCount + $completedPurchasesCount;

        // 3. Total Penjualan Hari Ini (Query Agregat Sum, hanya transaksi completed)
        $todaySalesTotal = (float) Sale::where('status', 'completed')
            ->whereDate('sold_at', today())
            ->sum('total');

        // 4. Total Penjualan Bulan Ini (Query Agregat Sum, hanya transaksi completed)
        $monthSalesTotal = (float) Sale::where('status', 'completed')
            ->whereYear('sold_at', now()->year)
            ->whereMonth('sold_at', now()->month)
            ->sum('total');

        // 5. Total Pengadaan / Pembelian Bulan Ini
        $monthPurchasesTotal = (float) Purchase::where('status', 'completed')
            ->whereYear('purchased_at', now()->year)
            ->whereMonth('purchased_at', now()->month)
            ->sum('total');

        // 6. Produk dengan Stok Rendah / Kritis (stock <= minimum_stock)
        $lowStockCount = Product::lowStock()->count();
        $lowStockProducts = Product::with('category')
            ->lowStock()
            ->orderBy('stock', 'asc')
            ->take(6)
            ->get();

        // 7. Transaksi Penjualan Terkini (Eager load user untuk hindari N+1)
        $recentSales = Sale::with('user')
            ->latest('sold_at')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'completedSalesCount',
            'completedPurchasesCount',
            'totalTransactionsCount',
            'todaySalesTotal',
            'monthSalesTotal',
            'monthPurchasesTotal',
            'lowStockCount',
            'lowStockProducts',
            'recentSales'
        ));
    }
}

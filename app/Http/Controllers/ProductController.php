<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar produk dengan eager loading kategori, pencarian, filter, dan pagination.
     */
    public function index(Request $request)
    {
        // Validasi input parameter filter pencarian di server
        $request->validate([
            'search' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'low_stock' => 'nullable|in:1',
        ]);

        // Eager load category untuk menghindari masalah N+1 Query
        $query = Product::with('category');

        // Pencarian aman berdasarkan SKU atau Nama Produk
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter berdasarkan status Stok Rendah (stock <= minimum_stock)
        if ($request->filled('low_stock') && $request->low_stock == '1') {
            $query->lowStock();
        }

        // Pagination 10 item per halaman dengan mempertahankan query string filter
        $products = $query->latest('id')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Tampilkan halaman khusus daftar produk stok rendah/kritis (stock <= minimum_stock).
     */
    public function lowStock(Request $request)
    {
        $query = Product::with('category')->lowStock();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('stock', 'asc')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.low-stock', compact('products', 'categories'));
    }

    /**
     * Form tambah produk baru (khusus Admin).
     */
    public function create()
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Akses ditolak: Hanya Admin yang dapat menambah produk.');

        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    /**
     * Simpan produk baru ke database (divalidasi dengan ProductRequest).
     */
    public function store(ProductRequest $request)
    {
        Product::create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Produk baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan rincian detail produk (bisa diakses Admin & Staff).
     */
    public function show(Product $product)
    {
        $product->load(['category', 'purchaseItems.purchase.supplier', 'saleItems.sale.user']);

        return view('products.show', compact('product'));
    }

    /**
     * Form edit produk (khusus Admin).
     */
    public function edit(Product $product)
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Akses ditolak: Hanya Admin yang dapat mengedit produk.');

        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Perbarui data produk (khusus Admin via ProductRequest).
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Data produk berhasil diperbarui.');
    }

    /**
     * Hapus produk dengan proteksi SoftDeletes jika sudah memiliki riwayat transaksi.
     */
    public function destroy(Product $product)
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Akses ditolak: Hanya Admin yang dapat menghapus produk.');

        if ($product->hasTransactions()) {
            // Menggunakan Soft Delete agar riwayat pembelian dan penjualan tidak rusak
            $product->delete();

            return redirect()->route('products.index')
                ->with('success', 'Produk memiliki riwayat transaksi sehingga diarsipkan secara aman (Soft Delete). Riwayat nota penjualan dan pembelian tetap valid.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}

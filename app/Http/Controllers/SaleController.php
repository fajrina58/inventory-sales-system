<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Tampilkan daftar transaksi penjualan dengan pagination, pencarian, dan filter tanggal/status.
     */
    public function index(Request $request)
    {
        // Validasi input parameter filter pencarian di server
        $request->validate([
            'search' => 'nullable|string|max:100',
            'status' => 'nullable|in:completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $query = Sale::with('user')->withCount('items');

        // Pencarian nomor nota transaksi atau nama kasir/staf
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter status (completed / cancelled)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter rentang tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('sold_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sold_at', '<=', $request->end_date);
        }

        $sales = $query->latest('sold_at')->paginate(10)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    /**
     * Form pembuatan transaksi penjualan baru (Stock-Out).
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('sales.create', compact('products'));
    }

    /**
     * Simpan transaksi penjualan, kurangi stok, dan hitung total via SaleService.
     */
    public function store(SaleRequest $request)
    {
        try {
            $sale = $this->saleService->createSale($request->validated(), $request->user());

            return redirect()->route('sales.show', $sale)
                ->with('success', "Penjualan {$sale->sale_number} berhasil diproses. Stok barang telah dikurangi.");
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses penjualan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan rincian nota faktur penjualan.
     */
    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Batalkan transaksi penjualan dan pulihkan stok barang (khusus Admin).
     */
    public function cancel(Sale $sale)
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Akses ditolak: Hanya Admin yang dapat membatalkan transaksi.');

        try {
            $this->saleService->cancelSale($sale, auth()->user());

            return back()->with('success', "Transaksi penjualan {$sale->sale_number} berhasil dibatalkan dan stok dikembalikan.");
        } catch (ValidationException $e) {
            $firstMessage = collect($e->errors())->flatten()->first();
            return back()->with('error', $firstMessage);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}

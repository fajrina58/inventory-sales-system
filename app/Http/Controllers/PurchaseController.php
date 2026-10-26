<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    /**
     * Tampilkan daftar transaksi pembelian dengan filter dan pagination.
     */
    public function index(Request $request)
    {
        // Validasi input filter pencarian di server
        $request->validate([
            'search' => 'nullable|string|max:100',
            'status' => 'nullable|in:completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $query = Purchase::with(['supplier', 'user'])->withCount('items');

        // Pencarian nomor faktur PO atau nama pemasok
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter status (completed / cancelled)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('purchased_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('purchased_at', '<=', $request->end_date);
        }

        $purchases = $query->latest('purchased_at')->paginate(10)->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Form pembuatan pembelian baru (Stock-In).
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    /**
     * Simpan transaksi pembelian dan tambah stok secara otomatis via Service.
     */
    public function store(PurchaseRequest $request)
    {
        try {
            $purchase = $this->purchaseService->createPurchase($request->validated(), $request->user());

            return redirect()->route('purchases.show', $purchase)
                ->with('success', "Pembelian {$purchase->purchase_number} berhasil dicatat dan stok telah bertambah.");
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memproses pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail transaksi faktur pembelian.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);

        return view('purchases.show', compact('purchase'));
    }

    /**
     * Batalkan transaksi pembelian dan pulihkan stok kembali (khusus Admin).
     */
    public function cancel(Purchase $purchase)
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Akses ditolak: Hanya Admin yang dapat membatalkan transaksi.');

        try {
            $this->purchaseService->cancelPurchase($purchase, auth()->user());

            return back()->with('success', "Transaksi {$purchase->purchase_number} berhasil dibatalkan dan stok dikurangi kembali.");
        } catch (ValidationException $e) {
            $firstMessage = collect($e->errors())->flatten()->first();
            return back()->with('error', $firstMessage);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}

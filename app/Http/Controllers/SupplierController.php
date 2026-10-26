<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Tampilkan daftar supplier/pemasok (bisa dilihat oleh Admin dan Staff).
     */
    public function index(Request $request)
    {
        $query = Supplier::withCount('purchases');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Form tambah supplier baru (khusus Admin).
     */
    public function create()
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Akses ditolak: Hanya Admin yang dapat menambah pemasok.');

        return view('suppliers.create');
    }

    /**
     * Simpan supplier baru (khusus Admin melalui SupplierRequest).
     */
    public function store(SupplierRequest $request)
    {
        Supplier::create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Pemasok berhasil ditambahkan.');
    }

    /**
     * Form edit supplier (khusus Admin).
     */
    public function edit(Supplier $supplier)
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Akses ditolak: Hanya Admin yang dapat mengubah data pemasok.');

        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Perbarui data supplier (khusus Admin).
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Data pemasok berhasil diperbarui.');
    }

    /**
     * Hapus data supplier (khusus Admin & dilindungi riwayat transaksi).
     */
    public function destroy(Supplier $supplier)
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Akses ditolak: Hanya Admin yang dapat menghapus pemasok.');

        if ($supplier->purchases()->exists()) {
            return back()->with('error', 'Pemasok tidak dapat dihapus karena memiliki riwayat transaksi pembelian.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Pemasok berhasil dihapus.');
    }
}

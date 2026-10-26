<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    /**
     * Membuat transaksi penjualan (Stock-Out) multi-item.
     * Menggunakan DB::transaction dan lockForUpdate() untuk mencegah overselling.
     */
    public function createSale(array $data, User $user): Sale
    {
        // Seluruh proses header, detail item, dan pengurangan stok dibungkus dalam satu transaksi basis data
        return DB::transaction(function () use ($data, $user) {
            // 1. Generate nomor nota penjualan unik (Format: TRX-YYYYMMDD-XXXX)
            $dateStr = now()->format('Ymd');
            $countToday = Sale::whereDate('sold_at', now()->toDateString())->count() + 1;
            $saleNumber = 'TRX-' . $dateStr . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            while (Sale::where('sale_number', $saleNumber)->exists()) {
                $countToday++;
                $saleNumber = 'TRX-' . $dateStr . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);
            }

            // 2. Buat header penjualan (total awal 0, dihitung ulang di server)
            $sale = Sale::create([
                'user_id' => $user->id,
                'sale_number' => $saleNumber,
                'total' => 0,
                'status' => 'completed',
                'sold_at' => now(),
            ]);

            $total = 0;

            // 3. Iterasi setiap item penjualan
            foreach ($data['items'] as $item) {
                // lockForUpdate(): Menerapkan Pessimistic Locking pada baris produk di MySQL
                // Mencegah dua transaksi kasir mengakses dan memotong sisa stok yang sama secara bersamaan (race condition)
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                $qty = (int) $item['quantity'];
                if ($qty < 1) {
                    throw ValidationException::withMessages([
                        'items' => ["Kuantitas produk '{$product->name}' minimal 1 unit."],
                    ]);
                }

                // Cek ketersediaan stok fisik sebelum dikurangi
                if ($product->stock < $qty) {
                    // Jika stok tidak cukup, lemparkan ValidationException
                    // DB::transaction akan otomatis me-rollback seluruh operasi yang sudah berjalan sebelumnya
                    throw ValidationException::withMessages([
                        'items' => [
                            "Penjualan ditolak: Stok untuk produk '{$product->name}' tidak mencukupi. " .
                            "(Tersedia: {$product->stock} unit, Diminta: {$qty} unit)."
                        ],
                    ]);
                }

                // HARGA RESMI DIAMBIL DARI DATABASE (selling_price), bukan dari input browser
                $unitPrice = (float) $product->selling_price;
                $subtotal = $qty * $unitPrice;

                // Simpan item nota penjualan
                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // Stok berkurang HANYA jika validasi stok lolos
                $product->decrement('stock', $qty);

                $total += $subtotal;
            }

            // 4. Perbarui total akhir pada header transaksi penjualan
            $sale->update(['total' => $total]);

            return $sale;
        });
    }

    /**
     * Membatalkan transaksi penjualan (khusus Admin) dan memulihkan stok kembali ke gudang.
     */
    public function cancelSale(Sale $sale, User $user): void
    {
        if (! $user->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Administrator yang berhak membatalkan transaksi penjualan.');
        }

        if ($sale->isCancelled()) {
            throw ValidationException::withMessages([
                'status' => ['Transaksi penjualan ini sudah berstatus batal (cancelled).'],
            ]);
        }

        // Pembatalan dan pengembalian stok dibungkus dalam DB::transaction
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                // Lock row produk saat mengembalikan stok
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                // Tambahkan kembali stok yang sebelumnya terjual
                $product->increment('stock', $item->quantity);
            }

            $sale->update(['status' => 'cancelled']);
        });
    }
}

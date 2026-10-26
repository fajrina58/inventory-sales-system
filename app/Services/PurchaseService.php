<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    /**
     * Membuat transaksi pembelian baru (Stock-In) beserta item dan penambahan stok.
     * Dijalankan dalam satu DB::transaction agar jika salah satu item gagal, seluruh data di-rollback.
     */
    public function createPurchase(array $data, User $user): Purchase
    {
        return DB::transaction(function () use ($data, $user) {
            // 1. Generate nomor purchase order unik (Format: PO-YYYYMMDD-XXXX)
            $dateStr = now()->format('Ymd');
            $countToday = Purchase::whereDate('purchased_at', now()->toDateString())->count() + 1;
            $purchaseNumber = 'PO-' . $dateStr . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            while (Purchase::where('purchase_number', $purchaseNumber)->exists()) {
                $countToday++;
                $purchaseNumber = 'PO-' . $dateStr . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);
            }

            // 2. Simpan header pembelian dengan total awal 0
            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'user_id' => $user->id,
                'purchase_number' => $purchaseNumber,
                'total' => 0,
                'status' => 'completed',
                'purchased_at' => now(),
            ]);

            $total = 0;

            // 3. Simpan item dan perbarui stok produk
            foreach ($data['items'] as $item) {
                // Lock row produk untuk mencegah race condition
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                $qty = (int) $item['quantity'];
                if ($qty <= 0) {
                    throw ValidationException::withMessages([
                        'items' => ["Kuantitas untuk produk '{$product->name}' harus lebih dari 0."],
                    ]);
                }

                $unitCost = (float) $item['unit_cost'];
                if ($unitCost < 0) {
                    throw ValidationException::withMessages([
                        'items' => ["Harga beli untuk produk '{$product->name}' tidak boleh negatif."],
                    ]);
                }

                // Subtotal dihitung secara ketat di server
                $subtotal = $qty * $unitCost;

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                // Stok bertambah secara otomatis saat pembelian berhasil
                $product->increment('stock', $qty);

                $total += $subtotal;
            }

            // 4. Perbarui total akhir header pembelian
            $purchase->update(['total' => $total]);

            return $purchase;
        });
    }

    /**
     * Membatalkan transaksi pembelian (khusus Admin) dan memulihkan stok kembali.
     * Menolak pembatalan jika stok fisik saat ini tidak mencukupi untuk dikurangi.
     */
    public function cancelPurchase(Purchase $purchase, User $user): void
    {
        if (! $user->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Administrator yang dapat membatalkan transaksi.');
        }

        if ($purchase->isCancelled()) {
            throw ValidationException::withMessages([
                'status' => ['Transaksi pembelian ini sudah berstatus batal (cancelled).'],
            ]);
        }

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                // Cek apakah stok fisik mencukupi untuk ditarik kembali
                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'stock' => [
                            "Pembatalan ditolak: Sisa stok produk '{$product->name}' saat ini ({$product->stock}) " .
                            "tidak mencukupi untuk dikurangi ({$item->quantity}). Stok tidak boleh negatif."
                        ],
                    ]);
                }

                // Kembalikan (kurangi) stok yang sebelumnya bertambah
                $product->decrement('stock', $item->quantity);
            }

            $purchase->update(['status' => 'cancelled']);
        });
    }
}

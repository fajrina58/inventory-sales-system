<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Faktur Pembelian: ') . $purchase->purchase_number }}
                    </h2>
                    @if ($purchase->isCompleted())
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Selesai (Completed)
                        </span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                            Dibatalkan (Cancelled)
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">Dicatat oleh {{ $purchase->user->name ?? '-' }} pada {{ $purchase->purchased_at->format('d M Y, H:i:s') }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('purchases.index') }}" class="px-3 py-1.5 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($purchase->isCancelled())
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-rose-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-rose-800">Transaksi Pembelian Telah Dibatalkan</h3>
                            <div class="mt-1 text-xs text-rose-700">
                                Transaksi ini telah dibatalkan oleh Administrator. Kuantitas barang dari faktur ini sudah ditarik/dikurangkan kembali dari stok sistem secara konsisten.
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Invoice Header Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Informasi Pemasok</h3>
                    <p class="text-base font-bold text-gray-900 mt-1">{{ $purchase->supplier->name ?? '-' }}</p>
                    <p class="text-xs text-gray-600 mt-0.5">Telepon: {{ $purchase->supplier->phone ?? '-' }}</p>
                    <p class="text-xs text-gray-600">Email: {{ $purchase->supplier->email ?? '-' }}</p>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rincian Transaksi</h3>
                    <p class="text-sm text-gray-700 mt-1"><span class="font-semibold">No. Faktur:</span> <span class="font-mono font-bold text-indigo-600">{{ $purchase->purchase_number }}</span></p>
                    <p class="text-sm text-gray-700 mt-0.5"><span class="font-semibold">Tanggal:</span> {{ $purchase->purchased_at->format('d F Y, H:i') }}</p>
                    <p class="text-sm text-gray-700"><span class="font-semibold">Petugas:</span> {{ $purchase->user->name ?? '-' }} ({{ ucfirst($purchase->user->role ?? '') }})</p>
                </div>

                <div class="sm:text-right">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Pembelian</h3>
                    <div class="text-2xl font-extrabold text-indigo-600 font-mono mt-1">
                        Rp {{ number_format($purchase->total, 0, ',', '.') }}
                    </div>
                    <span class="text-xs text-gray-500">Mencakup {{ $purchase->items->count() }} jenis barang</span>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Rincian Barang Masuk</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">No</th>
                                <th class="px-6 py-3 text-left">SKU</th>
                                <th class="px-6 py-3 text-left">Nama Produk</th>
                                <th class="px-6 py-3 text-center">Kuantitas Masuk</th>
                                <th class="px-6 py-3 text-right">Harga Beli Satuan</th>
                                <th class="px-6 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            @foreach ($purchase->items as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-bold text-gray-700">
                                        {{ $item->product->sku ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                        {{ $item->product->name ?? '(Produk Telah Dihapus)' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-emerald-600 font-mono">
                                        +{{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-mono text-gray-600">
                                        Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-mono font-bold text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-bold">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-right text-gray-800 uppercase text-xs">
                                    Grand Total:
                                </td>
                                <td class="px-6 py-4 text-right text-indigo-700 text-base font-mono">
                                    Rp {{ number_format($purchase->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Admin Actions: Cancel Transaction -->
            @if (Auth::user()->isAdmin() && $purchase->isCompleted())
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-amber-500">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Batalkan Transaksi Pembelian Ini</h4>
                            <p class="text-xs text-gray-500 mt-1">
                                Jika transaksi ini dibatalkan, status akan diubah menjadi <span class="font-bold text-rose-600">Cancelled</span> dan kuantitas produk akan ditarik kembali dari stok secara otomatis dalam satu transaksi basis data.
                            </p>
                        </div>
                        <form action="{{ route('purchases.cancel', $purchase) }}" method="POST"
                            onsubmit="return confirm('PERINGATAN: Membatalkan faktur ini akan mengurangi stok produk terkait kembali. Lanjutkan pembatalan?');">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold uppercase rounded-md shadow-sm transition">
                                Batalkan Transaksi
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

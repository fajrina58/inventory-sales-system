<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Detail Produk: ') . $product->name }}
                </h2>
                <p class="text-xs text-gray-500 font-mono mt-1">SKU: {{ $product->sku }} | Kategori: {{ $product->category->name ?? '-' }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('products.index') }}" class="px-3 py-1.5 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50">
                    &larr; Kembali
                </a>
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('products.edit', $product) }}" class="px-3 py-1.5 bg-indigo-600 rounded-md text-xs font-semibold text-white hover:bg-indigo-700">
                        Edit Produk
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Summary Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Stok -->
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 {{ $product->isLowStock() ? 'border-rose-500' : 'border-emerald-500' }}">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Sisa Stok Fisik</span>
                    <div class="text-2xl font-bold text-gray-900 mt-1 flex items-center gap-2">
                        {{ $product->stock }}
                        @if ($product->isLowStock())
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">Kritis</span>
                        @else
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Aman</span>
                        @endif
                    </div>
                    <span class="text-xs text-gray-500 mt-1 block">Batas Minimum: {{ $product->minimum_stock }} unit</span>
                </div>

                <!-- Harga Beli -->
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Harga Beli Master</span>
                    <div class="text-xl font-bold text-gray-900 font-mono mt-1">
                        Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                    </div>
                    <span class="text-xs text-gray-500 mt-1 block">Harga pokok acuan barang masuk</span>
                </div>

                <!-- Harga Jual -->
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-purple-500">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Harga Jual Satuan</span>
                    <div class="text-xl font-bold text-gray-900 font-mono mt-1">
                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                    </div>
                    <span class="text-xs text-gray-500 mt-1 block">Harga resmi nota penjualan</span>
                </div>

                <!-- Estimasi Margin -->
                @php
                    $margin = $product->selling_price - $product->purchase_price;
                    $marginPct = $product->purchase_price > 0 ? ($margin / $product->purchase_price) * 100 : 0;
                @endphp
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-amber-500">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Estimasi Margin Laba</span>
                    <div class="text-xl font-bold text-gray-900 font-mono mt-1">
                        Rp {{ number_format($margin, 0, ',', '.') }}
                    </div>
                    <span class="text-xs text-emerald-600 font-medium mt-1 block">+{{ number_format($marginPct, 1) }}% dari modal</span>
                </div>
            </div>

            <!-- History Tabs: Recent Purchases & Recent Sales -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Riwayat Pembelian Masuk -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center justify-between">
                        <span>Riwayat Pembelian Masuk (Stock-In)</span>
                        <span class="text-xs bg-blue-100 text-blue-800 font-semibold px-2 py-0.5 rounded-full">
                            {{ $product->purchaseItems->count() }} Transaksi
                        </span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-500 uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left">No. PO</th>
                                    <th class="px-3 py-2 text-left">Supplier</th>
                                    <th class="px-3 py-2 text-center">Qty</th>
                                    <th class="px-3 py-2 text-right">Biaya/Unit</th>
                                    <th class="px-3 py-2 text-right">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($product->purchaseItems->take(5) as $pi)
                                    <tr>
                                        <td class="px-3 py-2 font-mono font-bold text-indigo-600">
                                            {{ $pi->purchase->purchase_number ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-700">
                                            {{ $pi->purchase->supplier->name ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2 text-center font-bold text-emerald-600">
                                            +{{ $pi->quantity }}
                                        </td>
                                        <td class="px-3 py-2 text-right font-mono">
                                            Rp {{ number_format($pi->unit_cost, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-right text-gray-500">
                                            {{ $pi->purchase->purchased_at ? $pi->purchase->purchased_at->format('d/m/Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-gray-400">
                                            Belum ada riwayat pembelian untuk produk ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Riwayat Penjualan Keluar -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center justify-between">
                        <span>Riwayat Penjualan Keluar (Stock-Out)</span>
                        <span class="text-xs bg-purple-100 text-purple-800 font-semibold px-2 py-0.5 rounded-full">
                            {{ $product->saleItems->count() }} Transaksi
                        </span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-500 uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left">No. Nota</th>
                                    <th class="px-3 py-2 text-left">Kasir/Staf</th>
                                    <th class="px-3 py-2 text-center">Qty</th>
                                    <th class="px-3 py-2 text-right">Harga Jual</th>
                                    <th class="px-3 py-2 text-right">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($product->saleItems->take(5) as $si)
                                    <tr>
                                        <td class="px-3 py-2 font-mono font-bold text-indigo-600">
                                            {{ $si->sale->sale_number ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-700">
                                            {{ $si->sale->user->name ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2 text-center font-bold text-rose-600">
                                            -{{ $si->quantity }}
                                        </td>
                                        <td class="px-3 py-2 text-right font-mono">
                                            Rp {{ number_format($si->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-right text-gray-500">
                                            {{ $si->sale->sold_at ? $si->sale->sold_at->format('d/m/Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-gray-400">
                                            Belum ada transaksi penjualan untuk produk ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

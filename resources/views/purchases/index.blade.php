<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Riwayat Pembelian Barang (Stock-In)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Daftar transaksi pengadaan stok barang dari pemasok.</p>
            </div>
            <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Buat Pembelian Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Filter & Search Card -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                    <!-- Search input -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Cari No. PO / Supplier</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik No. PO atau supplier..."
                            class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status Transaksi</label>
                        <select name="status" class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Semua Status --</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                        </select>
                    </div>

                    <!-- Date range start -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Date range end -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-3 bg-gray-800 text-white rounded-md text-xs font-semibold uppercase hover:bg-gray-700 text-center">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'start_date', 'end_date']))
                            <a href="{{ route('purchases.index') }}" class="py-2 px-3 bg-gray-200 text-gray-700 rounded-md text-xs font-semibold uppercase hover:bg-gray-300 text-center flex items-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pembelian (PO)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemasok</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Biaya</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($purchases as $p)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-mono font-bold text-indigo-600">
                                            <a href="{{ route('purchases.show', $p) }}" class="hover:underline">
                                                {{ $p->purchase_number }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $p->supplier->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-600">
                                            {{ $p->user->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-xs text-gray-600">
                                            <span class="px-2 py-0.5 rounded-full bg-gray-100">
                                                {{ $p->items_count }} jenis
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-right font-mono font-bold text-gray-900">
                                            Rp {{ number_format($p->total, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-xs">
                                            @if ($p->isCompleted())
                                                <span class="px-2.5 py-1 font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 font-semibold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                                    Dibatalkan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $p->purchased_at ? $p->purchased_at->format('d M Y, H:i') : '-' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <a href="{{ route('purchases.show', $p) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold">
                                                Detail &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                            Belum ada transaksi pembelian yang tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $purchases->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

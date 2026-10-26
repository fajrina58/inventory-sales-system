<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-1.5 bg-rose-100 text-rose-700 rounded-lg text-lg">⚠️</span>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Daftar Produk Stok Rendah (Kritis)') }}
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-1">Daftar produk yang jumlah stok fisiknya telah mencapai atau berada di bawah batas minimum stok.</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('products.index') }}" class="px-3 py-1.5 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50">
                    &larr; Semua Produk
                </a>
                <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-indigo-600 rounded-md text-xs font-semibold text-white uppercase tracking-wider hover:bg-indigo-700 shadow-sm">
                    + Buat Pembelian (Stock-In)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Search & Filter Bar -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('products.low-stock') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Cari SKU / Nama</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik SKU atau nama..."
                            class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category_id" class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex gap-2">
                        <button type="submit" class="py-2 px-4 bg-gray-800 text-white rounded-md text-xs font-semibold uppercase hover:bg-gray-700">
                            Cari
                        </button>
                        @if(request()->hasAny(['search', 'category_id']))
                            <a href="{{ route('products.low-stock') }}" class="py-2 px-3 bg-gray-200 text-gray-700 rounded-md text-xs font-semibold uppercase hover:bg-gray-300 flex items-center">
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
                            <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-3 text-left">SKU</th>
                                    <th class="px-4 py-3 text-left">Nama Produk</th>
                                    <th class="px-4 py-3 text-left">Kategori</th>
                                    <th class="px-4 py-3 text-center">Sisa Stok Saat Ini</th>
                                    <th class="px-4 py-3 text-center">Batas Min. Stok</th>
                                    <th class="px-4 py-3 text-center">Defisit Stok</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($products as $p)
                                    <tr class="hover:bg-rose-50/40">
                                        <td class="px-4 py-4 whitespace-nowrap text-xs font-mono font-bold text-gray-700">
                                            {{ $p->sku }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            <a href="{{ route('products.show', $p) }}" class="hover:text-indigo-600">
                                                {{ $p->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-600">
                                            {{ $p->category->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                                {{ $p->stock }} unit
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-xs text-gray-600 font-mono">
                                            {{ $p->minimum_stock }} unit
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-xs font-bold text-rose-600 font-mono">
                                            @php
                                                $deficit = $p->minimum_stock - $p->stock;
                                            @endphp
                                            {{ $deficit >= 0 ? "-{$deficit} unit" : '0 unit' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-xs font-medium space-x-2">
                                            <a href="{{ route('products.show', $p) }}" class="text-blue-600 hover:text-blue-900 font-semibold">
                                                Detail
                                            </a>
                                            <a href="{{ route('purchases.create') }}" class="text-emerald-600 hover:text-emerald-900 font-semibold">
                                                + Restock
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                            🎉 Hebat! Tidak ada produk dengan stok kritis atau menipis saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

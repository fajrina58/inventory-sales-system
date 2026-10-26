<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Katalog Produk & Manajemen Stok') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Kelola data master barang dagang, harga jual/beli, dan pantau status persediaan.</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('products.low-stock') }}" class="inline-flex items-center px-3 py-2 bg-rose-50 border border-rose-300 rounded-md font-semibold text-xs text-rose-700 hover:bg-rose-100 transition">
                    ⚠️ Pantau Stok Rendah
                </a>
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        + Tambah Produk Baru
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Filter & Search Bar -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    <!-- Search input -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Pencarian SKU / Nama</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik SKU atau nama produk..."
                            class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Category filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Filter Kategori</label>
                        <select name="category_id" class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Low stock checkbox/filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status Persediaan</label>
                        <select name="low_stock" class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Status Stok</option>
                            <option value="1" {{ request('low_stock') == '1' ? 'selected' : '' }}>Hanya Stok Rendah (Kritis)</option>
                        </select>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-3 bg-gray-800 text-white rounded-md text-xs font-semibold uppercase hover:bg-gray-700 text-center">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'category_id', 'low_stock']))
                            <a href="{{ route('products.index') }}" class="py-2 px-3 bg-gray-200 text-gray-700 rounded-md text-xs font-semibold uppercase hover:bg-gray-300 text-center flex items-center">
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Beli</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Jual</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sisa Stok</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Min. Stok</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($products as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-xs font-mono font-bold text-gray-700">
                                            {{ $product->sku }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('products.show', $product) }}" class="hover:text-indigo-600">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-600">
                                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
                                                {{ $product->category->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-gray-600 font-mono">
                                            Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900 font-mono">
                                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold">
                                            @if ($product->isLowStock())
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                    ⚠️ {{ $product->stock }} (Kritis)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    {{ $product->stock }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-xs text-gray-500">
                                            {{ $product->minimum_stock }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                            <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900 text-xs">Detail</a>
                                            @if (Auth::user()->isAdmin())
                                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 text-xs">Edit</a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:text-rose-900 text-xs">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                            Tidak ada produk yang sesuai dengan kriteria pencarian.
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

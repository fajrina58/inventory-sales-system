<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <!-- Category -->
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori Produk</label>
                                <select name="category_id" id="category_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SKU -->
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700">Kode SKU Unik</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                                    placeholder="Contoh: PRD-MNM-001"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm uppercase font-mono focus:border-indigo-500 focus:ring-indigo-500">
                                @error('sku')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Product Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                placeholder="Contoh: Teh Botol Sosro 450ml (Karton/24)"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <!-- Purchase Price -->
                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700">Harga Beli Satuan (Rp)</label>
                                <input type="number" step="0.01" min="0" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" required
                                    placeholder="Contoh: 110000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm font-mono focus:border-indigo-500 focus:ring-indigo-500">
                                @error('purchase_price')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Selling Price -->
                            <div>
                                <label for="selling_price" class="block text-sm font-medium text-gray-700">Harga Jual Satuan (Rp)</label>
                                <input type="number" step="0.01" min="0" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" required
                                    placeholder="Contoh: 135000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm font-mono focus:border-indigo-500 focus:ring-indigo-500">
                                @error('selling_price')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <!-- Initial Stock -->
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700">Stok Awal</label>
                                <input type="number" min="0" name="stock" id="stock" value="{{ old('stock', 0) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm font-mono focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="text-xs text-gray-500 mt-1">Stok akan bertambah otomatis saat pembelian (stock-in) dan berkurang saat penjualan (stock-out).</p>
                                @error('stock')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Minimum Stock -->
                            <div>
                                <label for="minimum_stock" class="block text-sm font-medium text-gray-700">Ambang Batas Stok Minimum</label>
                                <input type="number" min="0" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', 5) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm font-mono focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="text-xs text-gray-500 mt-1">Peringatan stok kritis akan menyala jika stok saat ini <= angka ini.</p>
                                @error('minimum_stock')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Simpan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

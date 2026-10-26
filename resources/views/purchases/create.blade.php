<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Buat Pembelian Baru (Stock-In)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Pilih pemasok, tambahkan produk yang dibeli, dan stok produk akan bertambah otomatis.</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="px-3 py-1.5 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        products: {{ Js::from($products) }},
        items: [
            { product_id: '', quantity: 1, unit_cost: 0 }
        ],
        addItem() {
            this.items.push({ product_id: '', quantity: 1, unit_cost: 0 });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        onProductChange(index) {
            const prodId = this.items[index].product_id;
            const found = this.products.find(p => p.id == prodId);
            if (found) {
                this.items[index].unit_cost = Number(found.purchase_price);
            }
        },
        subtotal(item) {
            return (Number(item.quantity) || 0) * (Number(item.unit_cost) || 0);
        },
        get grandTotal() {
            return this.items.reduce((acc, item) => acc + this.subtotal(item), 0);
        },
        formatRupiah(amount) {
            return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('purchases.store') }}" method="POST">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
                    <!-- Supplier Selection & Petugas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b pb-6">
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700">Pilih Pemasok / Distributor *</label>
                            <select name="supplier_id" id="supplier_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">-- Pilih Pemasok --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }} ({{ $supplier->phone ?? 'Tanpa no. telepon' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Petugas Pencatat</label>
                            <input type="text" readonly disabled value="{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})"
                                class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-500 text-sm">
                            <span class="text-xs text-gray-400 mt-1 block">Waktu dan petugas transaksi dicatat otomatis oleh sistem.</span>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-base font-bold text-gray-900">Daftar Produk Masuk</h3>
                            <button type="button" @click="addItem()"
                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md shadow-sm">
                                + Tambah Baris Produk
                            </button>
                        </div>

                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Pilih Produk</th>
                                        <th class="px-4 py-3 text-center font-semibold w-32">Kuantitas</th>
                                        <th class="px-4 py-3 text-right font-semibold w-48">Harga Beli Satuan (Rp)</th>
                                        <th class="px-4 py-3 text-right font-semibold w-48">Subtotal (Rp)</th>
                                        <th class="px-4 py-3 text-center font-semibold w-16">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <!-- Product Select -->
                                            <td class="px-4 py-3">
                                                <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="onProductChange(index)" required
                                                    class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="">-- Pilih Produk --</option>
                                                    <template x-for="p in products" :key="p.id">
                                                        <option :value="p.id" x-text="`${p.sku} - ${p.name} (Stok: ${p.stock})`"></option>
                                                    </template>
                                                </select>
                                            </td>

                                            <!-- Quantity -->
                                            <td class="px-4 py-3">
                                                <input type="number" min="1" :name="`items[${index}][quantity]`" x-model.number="item.quantity" required
                                                    class="w-full text-center rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                                            </td>

                                            <!-- Unit Cost -->
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" min="0" :name="`items[${index}][unit_cost]`" x-model.number="item.unit_cost" required
                                                    class="w-full text-right rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                                            </td>

                                            <!-- Subtotal Preview -->
                                            <td class="px-4 py-3 text-right font-mono font-semibold text-gray-900" x-text="formatRupiah(subtotal(item))">
                                            </td>

                                            <!-- Remove Row -->
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" @click="removeItem(index)" :disabled="items.length <= 1"
                                                    :class="items.length <= 1 ? 'opacity-30 cursor-not-allowed' : 'text-rose-600 hover:text-rose-800'"
                                                    class="p-1 font-bold">
                                                    &times;
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Grand Total Summary -->
                        <div class="mt-4 flex justify-end">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 w-full sm:w-1/3 space-y-2">
                                <div class="flex justify-between items-center text-sm text-gray-600">
                                    <span>Total Item:</span>
                                    <span class="font-bold" x-text="`${items.length} macam`"></span>
                                </div>
                                <div class="flex justify-between items-center text-lg font-bold text-gray-900 border-t pt-2">
                                    <span>Total Pembelian:</span>
                                    <span class="text-indigo-600 font-mono" x-text="formatRupiah(grandTotal)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('purchases.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-md">
                            Konfirmasi & Simpan Pembelian
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

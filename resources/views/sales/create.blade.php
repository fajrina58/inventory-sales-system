<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Transaksi Penjualan Baru (Stock-Out)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Pilih produk dan tentukan jumlah barang terjual. Stok akan berkurang otomatis dan harga dihitung dari database.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="px-3 py-1.5 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        products: {{ Js::from($products) }},
        items: [
            { product_id: '', quantity: 1, unit_price: 0, stock: 0 }
        ],
        addItem() {
            this.items.push({ product_id: '', quantity: 1, unit_price: 0, stock: 0 });
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
                this.items[index].unit_price = Number(found.selling_price);
                this.items[index].stock = Number(found.stock);
            } else {
                this.items[index].unit_price = 0;
                this.items[index].stock = 0;
            }
        },
        subtotal(item) {
            return (Number(item.quantity) || 0) * (Number(item.unit_price) || 0);
        },
        get grandTotal() {
            return this.items.reduce((acc, item) => acc + this.subtotal(item), 0);
        },
        get hasInsufficientStock() {
            return this.items.some(item => item.product_id && item.quantity > item.stock);
        },
        formatRupiah(amount) {
            return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('sales.store') }}" method="POST">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
                    <!-- Cashier / Info Header -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b pb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kasir / Petugas</label>
                            <input type="text" readonly disabled value="{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})"
                                class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-500 text-sm">
                            <span class="text-xs text-gray-400 mt-1 block">Waktu penjualan dicatat saat transaksi dikonfirmasi di server.</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Metode Validasi Harga & Stok</label>
                            <div class="mt-1 p-2.5 bg-blue-50 border border-blue-200 rounded-md text-xs text-blue-800">
                                <strong>Keamanan Server:</strong> Harga jual diambil resmi dari basis data master. Sistem otomatis mengunci baris produk (<code>lockForUpdate</code>) untuk memastikan stok tidak minus.
                            </div>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-base font-bold text-gray-900">Daftar Produk Terjual</h3>
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
                                        <th class="px-4 py-3 text-center font-semibold w-28">Sisa Stok</th>
                                        <th class="px-4 py-3 text-center font-semibold w-32">Kuantitas Jual</th>
                                        <th class="px-4 py-3 text-right font-semibold w-48">Harga Jual Resmi</th>
                                        <th class="px-4 py-3 text-right font-semibold w-48">Subtotal</th>
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
                                                        <option :value="p.id" x-text="`${p.sku} - ${p.name} (Sisa: ${p.stock})`"></option>
                                                    </template>
                                                </select>
                                                <div x-show="item.product_id && item.quantity > item.stock" class="text-xs text-rose-600 font-bold mt-1">
                                                    ⚠️ Kuantitas melebihi stok yang tersedia!
                                                </div>
                                            </td>

                                            <!-- Available Stock badge -->
                                            <td class="px-4 py-3 text-center font-mono">
                                                <span class="px-2 py-0.5 rounded text-xs font-bold"
                                                    :class="item.stock <= 0 ? 'bg-rose-100 text-rose-700' : 'bg-gray-100 text-gray-800'"
                                                    x-text="item.product_id ? item.stock : '-'"></span>
                                            </td>

                                            <!-- Quantity -->
                                            <td class="px-4 py-3">
                                                <input type="number" min="1" :name="`items[${index}][quantity]`" x-model.number="item.quantity" required
                                                    class="w-full text-center rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono font-bold"
                                                    :class="item.quantity > item.stock ? 'border-rose-500 bg-rose-50 text-rose-700' : ''">
                                            </td>

                                            <!-- Unit Price (read-only from database) -->
                                            <td class="px-4 py-3 text-right font-mono text-gray-700" x-text="formatRupiah(item.unit_price)">
                                            </td>

                                            <!-- Subtotal Preview -->
                                            <td class="px-4 py-3 text-right font-mono font-bold text-gray-900" x-text="formatRupiah(subtotal(item))">
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
                        <div class="mt-4 flex justify-between items-start">
                            <div class="text-xs text-gray-500 max-w-sm">
                                <p>Pastikan jumlah kuantitas tidak melebihi persediaan stok fisik. Server akan memvalidasi stok dan menolak transaksi jika stok tidak mencukupi.</p>
                            </div>

                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 w-full sm:w-1/3 space-y-2">
                                <div class="flex justify-between items-center text-sm text-gray-600">
                                    <span>Total Macam Barang:</span>
                                    <span class="font-bold" x-text="`${items.length} item`"></span>
                                </div>
                                <div class="flex justify-between items-center text-lg font-bold text-gray-900 border-t pt-2">
                                    <span>Grand Total:</span>
                                    <span class="text-indigo-600 font-mono" x-text="formatRupiah(grandTotal)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('sales.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-md">
                            Konfirmasi & Proses Penjualan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

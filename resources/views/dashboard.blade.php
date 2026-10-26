<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-[#1e2a4a] leading-tight tracking-tight">
                    {{ __('Dashboard Sistem Distribusi & Inventori') }}
                </h2>
                <p class="text-xs sm:text-sm text-[#6b7799] mt-1">
                    Selamat datang kembali, <strong class="text-[#2f6df6] font-semibold">{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }}).
                </p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-[#2f6df6] to-[#6c9bff] hover:from-blue-600 hover:to-blue-500 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm shadow-blue-500/25 hover:shadow-md transition-all active:scale-[0.98] whitespace-nowrap">
                    <span>+</span>
                    <span>Penjualan Baru</span>
                </a>
                <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm shadow-emerald-600/20 hover:shadow-md transition-all active:scale-[0.98] whitespace-nowrap">
                    <span>+</span>
                    <span>Pembelian (Stock-In)</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Summary Metric Cards (Dihitung Real-time dari DB) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Penjualan Hari Ini (Kartu Pendapatan Utama: Menonjol dengan Gradasi Biru dan Teks Putih) -->
                <div class="bg-gradient-to-br from-[#2f6df6] to-[#6c9bff] text-white p-6 rounded-2xl shadow-lg shadow-blue-500/15 relative overflow-hidden flex flex-col justify-between">
                    <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-100">Penjualan Hari Ini</span>
                            <div class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1 text-white">
                                Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}
                            </div>
                        </div>
                        <span class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-lg border border-white/25 shadow-inner shrink-0">
                            💰
                        </span>
                    </div>
                    <span class="text-xs text-blue-100/90 font-medium mt-4 block relative z-10">
                        Transaksi selesai hari ini
                    </span>
                </div>

                <!-- Penjualan Bulan Ini -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#6b7799]">Penjualan Bulan Ini</span>
                            <div class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1 text-[#1e2a4a]">
                                Rp {{ number_format($monthSalesTotal, 0, ',', '.') }}
                            </div>
                        </div>
                        <span class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg border border-blue-100/60 shrink-0">
                            📈
                        </span>
                    </div>
                    <span class="text-xs text-[#6b7799] mt-4 block">
                        Bulan {{ now()->translatedFormat('F Y') }}
                    </span>
                </div>

                <!-- Total Transaksi Selesai -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#6b7799]">Transaksi Sukses</span>
                            <div class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1 text-[#1e2a4a]">
                                {{ number_format($totalTransactionsCount, 0, ',', '.') }}
                            </div>
                        </div>
                        <span class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg border border-indigo-100/60 shrink-0">
                            📑
                        </span>
                    </div>
                    <div class="text-xs text-[#6b7799] mt-4 flex justify-between items-center pt-2 border-t border-gray-100">
                        <span>Penjualan: <strong class="text-[#1e2a4a]">{{ $completedSalesCount }}</strong></span>
                        <span>Pembelian: <strong class="text-[#1e2a4a]">{{ $completedPurchasesCount }}</strong></span>
                    </div>
                </div>

                <!-- Produk Stok Rendah -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#6b7799]">Produk Stok Kritis</span>
                            <div class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1 {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-[#1e2a4a]' }}">
                                {{ $lowStockCount }}
                            </div>
                        </div>
                        <span class="w-11 h-11 rounded-xl {{ $lowStockCount > 0 ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }} flex items-center justify-center text-lg shrink-0">
                            ⚠️
                        </span>
                    </div>
                    <div class="mt-4 flex justify-between items-center text-xs pt-2 border-t border-gray-100">
                        <span class="text-[#6b7799]">Total Produk: <strong class="text-[#1e2a4a]">{{ $totalProducts }}</strong></span>
                        @if($lowStockCount > 0)
                            <a href="{{ route('products.low-stock') }}" class="text-rose-600 font-bold hover:underline inline-flex items-center gap-1">
                                Lihat Semua &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Daftar Produk Stok Rendah / Kritis -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                            <div>
                                <h3 class="text-base font-bold text-[#1e2a4a] flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center text-sm shrink-0">⚠️</span>
                                    Peringatan Stok Rendah
                                </h3>
                                <p class="text-xs text-[#6b7799] mt-0.5">Produk yang persediaannya telah menyentuh batas minimum stok.</p>
                            </div>
                            <a href="{{ route('products.low-stock') }}" class="text-xs text-[#2f6df6] font-semibold hover:text-blue-700 hover:underline shrink-0">
                                Buka Daftar Lengkap &rarr;
                            </a>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="min-w-full text-xs divide-y divide-gray-100">
                                <thead class="bg-gray-50/80 text-[#6b7799] uppercase text-[11px] font-bold tracking-wider">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left">SKU</th>
                                        <th scope="col" class="px-4 py-3 text-left">Nama Produk</th>
                                        <th scope="col" class="px-4 py-3 text-right">Sisa Stok</th>
                                        <th scope="col" class="px-4 py-3 text-right">Batas Min</th>
                                        <th scope="col" class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse ($lowStockProducts as $lp)
                                        <tr class="hover:bg-rose-50/30 transition-colors">
                                            <td class="px-4 py-3 font-mono font-semibold text-[#6b7799] whitespace-nowrap">
                                                {{ $lp->sku }}
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-[#1e2a4a]">
                                                <a href="{{ route('products.show', $lp) }}" class="hover:text-[#2f6df6] transition-colors">
                                                    {{ $lp->name }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/70">
                                                    {{ $lp->stock }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right text-[#6b7799] font-medium whitespace-nowrap">
                                                {{ $lp->minimum_stock }}
                                            </td>
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                <a href="{{ route('purchases.create') }}" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-xs shadow-sm shadow-emerald-600/20 transition-all active:scale-[0.98] whitespace-nowrap">
                                                    <span>+</span>
                                                    <span>Restock</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                          <td colspan="5" class="px-4 py-10 text-center text-[#6b7799]">
                                                🎉 Persediaan aman! Tidak ada produk yang berada di bawah stok minimum.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Transaksi Penjualan Terakhir -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                            <div>
                                <h3 class="text-base font-bold text-[#1e2a4a] flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-[#2f6df6] flex items-center justify-center text-sm shrink-0">🛒</span>
                                    Transaksi Penjualan Terkini
                                </h3>
                                <p class="text-xs text-[#6b7799] mt-0.5">Riwayat nota transaksi penjualan terbaru di kasir.</p>
                            </div>
                            <a href="{{ route('sales.index') }}" class="text-xs text-[#2f6df6] font-semibold hover:text-blue-700 hover:underline shrink-0">
                                Semua Penjualan &rarr;
                            </a>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="min-w-full text-xs divide-y divide-gray-100">
                                <thead class="bg-gray-50/80 text-[#6b7799] uppercase text-[11px] font-bold tracking-wider">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left">No. Nota</th>
                                        <th scope="col" class="px-4 py-3 text-left">Kasir</th>
                                        <th scope="col" class="px-4 py-3 text-right">Total</th>
                                        <th scope="col" class="px-4 py-3 text-center">Status</th>
                                        <th scope="col" class="px-4 py-3 text-right">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse ($recentSales as $rs)
                                        <tr class="hover:bg-blue-50/30 transition-colors">
                                            <td class="px-4 py-3 font-mono font-bold text-[#2f6df6] whitespace-nowrap">
                                                <a href="{{ route('sales.show', $rs) }}" class="hover:underline">
                                                    {{ $rs->sale_number }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-[#1e2a4a] font-medium whitespace-nowrap">
                                                {{ $rs->user->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono font-bold text-[#1e2a4a] whitespace-nowrap">
                                                Rp {{ number_format($rs->total, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                @if ($rs->isCompleted())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                                        Selesai
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/80">
                                                        Batal
                                                    </span>
                                                @endif
                                          </td>
                                            <td class="px-4 py-3 text-right text-[#6b7799] whitespace-nowrap">
                                                {{ $rs->sold_at->format('d/m H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-10 text-center text-[#6b7799]">
                                                Belum ada transaksi penjualan yang diproses.
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
    </div>
</x-app-layout>

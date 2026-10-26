<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'StokPintar') }}</title>

        <!-- Fonts: Plus Jakarta Sans from Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#f4f7ff] text-gray-900 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl shadow-blue-900/10 border border-blue-100/60 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[640px]">
            
            <!-- Panel Kiri: Gradasi Biru (#2f6df6 ke #6c9bff) - Disembunyikan di layar HP, tampil di layar lebar -->
            <div class="hidden lg:flex lg:col-span-5 bg-gradient-to-br from-[#2f6df6] to-[#6c9bff] p-10 flex-col justify-between text-white relative overflow-hidden select-none">
                <!-- Background decorative shapes -->
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-20 -right-20 w-72 h-72 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.15),transparent_50%)] pointer-events-none"></div>

                <!-- Header: Logo + Nama Aplikasi -->
                <div class="relative z-10">
                    <a href="/" class="inline-flex items-center gap-3 group">
                        <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md p-2 flex items-center justify-center border border-white/20 shadow-md group-hover:scale-105 transition-transform duration-200">
                            <x-application-logo class="w-full h-full" />
                        </div>
                        <div>
                            <span class="block text-2xl font-extrabold tracking-tight text-white drop-shadow-sm">{{ config('app.name', 'StokPintar') }}</span>
                            <span class="block text-xs font-medium text-blue-100/90 tracking-wider uppercase">Sistem Inventaris & Penjualan</span>
                        </div>
                    </a>
                </div>

                <!-- Bagian Tengah: Tagline & 3 Poin Fitur dengan Ikon -->
                <div class="relative z-10 my-8">
                    <div class="mb-7">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm text-white border border-white/25 mb-3">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                            Modern & Handal
                        </span>
                        <h2 class="text-2xl font-bold leading-snug text-white">
                            Kelola stok & penjualan lebih rapi
                        </h2>
                        <p class="mt-2 text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                            Solusi cerdas terpadu untuk efisiensi bisnis, pencatatan kasir, dan pengawasan stok gudang akurat.
                        </p>
                    </div>

                    <!-- 3 Poin Fitur dengan Ikon -->
                    <div class="space-y-3.5">
                        <!-- Fitur 1: Stok Real-time -->
                        <div class="flex items-start gap-3 bg-white/10 backdrop-blur-sm p-3.5 rounded-2xl border border-white/15 transition-all duration-200 hover:bg-white/15">
                            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0 text-white shadow-inner">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-white">Stok Real-time</h4>
                                <p class="text-xs text-blue-100/80 leading-relaxed mt-0.5">Pantau jumlah persediaan & peringatan stok menipis secara instan.</p>
                            </div>
                        </div>

                        <!-- Fitur 2: Pembelian & Penjualan -->
                        <div class="flex items-start gap-3 bg-white/10 backdrop-blur-sm p-3.5 rounded-2xl border border-white/15 transition-all duration-200 hover:bg-white/15">
                            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0 text-white shadow-inner">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-white">Pembelian & Penjualan</h4>
                                <p class="text-xs text-blue-100/80 leading-relaxed mt-0.5">Catat transaksi masuk dan keluar otomatis dengan kalkulasi harga pas.</p>
                            </div>
                        </div>

                        <!-- Fitur 3: Laporan Dashboard -->
                        <div class="flex items-start gap-3 bg-white/10 backdrop-blur-sm p-3.5 rounded-2xl border border-white/15 transition-all duration-200 hover:bg-white/15">
                            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0 text-white shadow-inner">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-white">Laporan Dashboard</h4>
                                <p class="text-xs text-blue-100/80 leading-relaxed mt-0.5">Visualisasi pendapatan, produk terlaris, dan arus transaksi dalam satu layar.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer: Copyright & Versi -->
                <div class="relative z-10 pt-4 border-t border-white/15 flex items-center justify-between text-xs text-blue-100/70">
                    <span>&copy; {{ date('Y') }} {{ config('app.name', 'StokPintar') }}</span>
                    <span>v1.0.0</span>
                </div>
            </div>

            <!-- Panel Kanan: Kartu Form Putih Bersih -->
            <div class="lg:col-span-7 p-6 sm:p-10 lg:p-12 flex flex-col justify-center bg-white relative">
                <!-- Tampilan Mobile (HP): Panel Kiri Sembunyi, Logo Tampil di Atas Form -->
                <div class="lg:hidden flex flex-col items-center justify-center text-center mb-6">
                    <a href="/" class="inline-flex items-center gap-2.5 mb-2">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 p-2 flex items-center justify-center shadow-sm">
                            <x-application-logo class="w-full h-full" />
                        </div>
                        <span class="text-2xl font-bold text-gray-900 tracking-tight">{{ config('app.name', 'StokPintar') }}</span>
                    </a>
                    <p class="text-xs text-gray-500 font-medium">Sistem Inventaris & Penjualan</p>
                </div>

                <!-- Konten Form -->
                <div class="w-full max-w-md mx-auto">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </body>
</html>

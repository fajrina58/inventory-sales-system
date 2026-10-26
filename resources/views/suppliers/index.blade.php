<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Pemasok (Suppliers)') }}
            </h2>
            @if (Auth::user()->isAdmin())
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    + Tambah Pemasok
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Search Filter -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('suppliers.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, telepon, atau email..."
                        class="w-full sm:w-1/3 rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-xs font-semibold uppercase hover:bg-gray-700">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-xs font-semibold uppercase hover:bg-gray-300 flex items-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemasok</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Transaksi Pembelian</th>
                                    @if (Auth::user()->isAdmin())
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($suppliers as $index => $supplier)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $suppliers->firstItem() + $index }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ $supplier->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $supplier->phone ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $supplier->email ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $supplier->purchases_count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $supplier->purchases_count }} Transaksi
                                            </span>
                                        </td>
                                        @if (Auth::user()->isAdmin())
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                                <a href="{{ route('suppliers.edit', $supplier) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemasok ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:text-rose-900">Hapus</button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->isAdmin() ? 6 : 5 }}" class="px-6 py-8 text-center text-gray-500">
                                            Belum ada data pemasok yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $suppliers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

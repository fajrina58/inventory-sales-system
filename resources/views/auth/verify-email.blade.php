<x-guest-layout>
    <!-- Header Form -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
            Verifikasi Email ✉️
        </h1>
        <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">
            {{ __('Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan ke email Anda.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm font-medium text-emerald-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ __('Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda daftarkan.') }}</span>
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="w-full inline-flex justify-center items-center py-3.5 px-5 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-blue-600 via-[#2f6df6] to-[#6c9bff] hover:from-blue-700 hover:via-blue-600 hover:to-blue-500 active:scale-[0.99] shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 cursor-pointer">
                <span>{{ __('Kirim Ulang Email Verifikasi') }}</span>
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center pt-2">
            @csrf

            <button type="submit" class="text-sm font-semibold text-gray-500 hover:text-gray-800 hover:underline transition-colors">
                {{ __('Keluar (Log Out)') }}
            </button>
        </form>
    </div>
</x-guest-layout>

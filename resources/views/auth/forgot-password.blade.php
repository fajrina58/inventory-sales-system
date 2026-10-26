<x-guest-layout>
    <!-- Header Form -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
            Lupa Kata Sandi? 🔑
        </h1>
        <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">
            Tidak masalah. Masukkan alamat email yang terdaftar dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider mb-1.5" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                    </svg>
                </div>
                <input id="email" 
                       class="block w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm placeholder-gray-400 text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm @error('email') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       placeholder="nama@perusahaan.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Tombol Kirim Tautan: Biru gradasi, lebar penuh, rounded-xl, efek hover -->
        <div class="pt-2">
            <button type="submit" class="w-full inline-flex justify-center items-center py-3.5 px-5 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-blue-600 via-[#2f6df6] to-[#6c9bff] hover:from-blue-700 hover:via-blue-600 hover:to-blue-500 active:scale-[0.99] shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 cursor-pointer">
                <span>{{ __('Kirim Tautan Reset Password') }}</span>
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </button>
        </div>

        <div class="text-center pt-4 border-t border-gray-100">
            <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                <svg class="mr-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke halaman Masuk
            </a>
        </div>
    </form>
</x-guest-layout>

<x-guest-layout>
    <!-- Header Form -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
            Selamat datang kembali 👋
        </h1>
        <p class="text-sm text-gray-500 mt-1.5">
            Silakan masuk untuk mengelola persediaan dan transaksi Anda.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
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
                       autocomplete="username" 
                       placeholder="nama@perusahaan.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <x-input-label for="password" :value="__('Password')" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors" href="{{ route('password.request') }}">
                        {{ __('Lupa kata sandi?') }}
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password" 
                       class="block w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm placeholder-gray-400 text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm @error('password') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror"
                       type="password"
                       name="password"
                       required 
                       autocomplete="current-password" 
                       placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center pt-0.5">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 focus:ring-offset-0 cursor-pointer" name="remember">
                <span class="ms-2.5 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <!-- Tombol Masuk: Biru gradasi, lebar penuh, rounded-xl, efek hover -->
        <div class="pt-2">
            <button type="submit" class="w-full inline-flex justify-center items-center py-3.5 px-5 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-blue-600 via-[#2f6df6] to-[#6c9bff] hover:from-blue-700 hover:via-blue-600 hover:to-blue-500 active:scale-[0.99] shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 cursor-pointer">
                <span>{{ __('Masuk') }}</span>
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>

        @if (Route::has('register'))
            <div class="text-center pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline ml-1">
                        Daftar sekarang
                    </a>
                </p>
            </div>
        @endif
    </form>
</x-guest-layout>

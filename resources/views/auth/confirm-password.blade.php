<x-guest-layout>
    <!-- Header Form -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
            Konfirmasi Kata Sandi 🛡️
        </h1>
        <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">
            Ini adalah area aman aplikasi. Harap masukkan kata sandi Anda sebelum melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider mb-1.5" />
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

        <div class="pt-2">
            <button type="submit" class="w-full inline-flex justify-center items-center py-3.5 px-5 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-blue-600 via-[#2f6df6] to-[#6c9bff] hover:from-blue-700 hover:via-blue-600 hover:to-blue-500 active:scale-[0.99] shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 cursor-pointer">
                <span>{{ __('Konfirmasi') }}</span>
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </form>
</x-guest-layout>

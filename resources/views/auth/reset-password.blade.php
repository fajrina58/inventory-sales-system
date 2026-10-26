<x-guest-layout>
    <!-- Header Form -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
            Perbarui Kata Sandi 🔒
        </h1>
        <p class="text-sm text-gray-500 mt-1.5">
            Silakan buat kata sandi baru untuk akun Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                       value="{{ old('email', $request->email) }}" 
                       required 
                       autofocus 
                       autocomplete="username" 
                       placeholder="nama@perusahaan.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Kata Sandi Baru')" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider mb-1.5" />
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
                       autocomplete="new-password" 
                       placeholder="Minimal 8 karakter" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi Baru')" class="block font-semibold text-xs text-gray-700 uppercase tracking-wider mb-1.5" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <input id="password_confirmation" 
                       class="block w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm placeholder-gray-400 text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm @error('password_confirmation') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror"
                       type="password"
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password" 
                       placeholder="Ulangi kata sandi baru" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full inline-flex justify-center items-center py-3.5 px-5 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-blue-600 via-[#2f6df6] to-[#6c9bff] hover:from-blue-700 hover:via-blue-600 hover:to-blue-500 active:scale-[0.99] shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 cursor-pointer">
                <span>{{ __('Reset Kata Sandi') }}</span>
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </form>
</x-guest-layout>

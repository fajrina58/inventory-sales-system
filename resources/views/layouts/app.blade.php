<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'StokPintar') }}</title>

        <!-- Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-[#1e2a4a]">
        <div class="min-h-screen bg-[#f4f7ff]">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-gray-100/80 shadow-xs">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                <div class="max-w-7xl mx-auto pt-4 px-4 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-md shadow-sm">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-emerald-800 text-sm font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-md shadow-sm">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-rose-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-rose-800 text-sm font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-md shadow-sm">
                            <div class="font-medium text-amber-800 text-sm mb-1">Perhatian: Ada kesalahan input:</div>
                            <ul class="list-disc list-inside text-xs text-amber-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{ $slot }}
            </main>
        </div>
    </body>
</html>

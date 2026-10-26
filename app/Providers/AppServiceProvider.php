<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate untuk hak akses khusus Administrator
        \Illuminate\Support\Facades\Gate::define('admin-only', function (\App\Models\User $user) {
            return $user->isAdmin();
        });

        // Gate untuk pembatalan transaksi (hanya Admin)
        \Illuminate\Support\Facades\Gate::define('cancel-transaction', function (\App\Models\User $user) {
            return $user->isAdmin();
        });

        // Gate untuk manajemen data master (hanya Admin)
        \Illuminate\Support\Facades\Gate::define('manage-master', function (\App\Models\User $user) {
            return $user->isAdmin();
        });

        // Gate untuk manajemen pengguna (hanya Admin)
        \Illuminate\Support\Facades\Gate::define('manage-users', function (\App\Models\User $user) {
            return $user->isAdmin();
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }
        Inertia::share([
            'appName' => config('app.name'),
            'appVersion' => config('app.version'),
            'user' => function () {
                return auth()->user() ? [
                    'id' => auth()->user()->id,
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ] : null;
            },
            'selectedWarehouse' => function () {
                return session('selectedWarehouse') ? [
                    'id' => session('selectedWarehouse')->id,
                    'name' => session('selectedWarehouse')->name,
                ] : null;
            }
        ]);
    }
}

<?php

namespace App\Providers;

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

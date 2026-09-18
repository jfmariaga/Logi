<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        // SuperAdmin bypass para todos los permisos
        Gate::before(function ($user, $ability) {
            return $user->hasRole('SuperAdmin') ? true : null;
        });

        // El hosting sirve la app detrás de un proxy/SSL terminado antes de llegar a PHP,
        // así que Laravel ve la petición como HTTP y firma URLs (previews de Livewire,
        // rutas firmadas, etc.) con http:// aunque APP_URL sea https, rompiendo su validación.
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}

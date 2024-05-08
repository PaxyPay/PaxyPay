<?php

namespace App\Providers;

use App\Http\Middleware\CustomSessionMiddleware;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // $this->app->singleton('session', function ($app) {
        //     return new CustomSessionMiddleware($app->make(SessionManager::class));
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }
}

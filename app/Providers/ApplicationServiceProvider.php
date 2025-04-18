<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Application\Services\UserService;
use App\Application\Services\RoleService;
use App\Application\Services\EAVService;
use App\Application\Services\AuthService;

class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UserService::class);
        $this->app->singleton(RoleService::class);
        $this->app->singleton(EAVService::class);
        $this->app->singleton(AuthService::class);

        // Register EAV service in the container with a name for easier access
        $this->app->singleton('eav.service', function ($app) {
            return $app->make(EAVService::class);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

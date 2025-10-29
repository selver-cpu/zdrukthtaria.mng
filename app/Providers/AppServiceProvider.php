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
        $this->app->singleton(\App\Services\CuttingOptimizationService::class, function ($app) {
            return new \App\Services\CuttingOptimizationService();
        });
        
        $this->app->singleton(\App\Services\SmsService::class, function ($app) {
            return new \App\Services\SmsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

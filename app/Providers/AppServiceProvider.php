<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\HavanaFerriesService;
use App\Services\BananaLinesService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FerryServiceInterface::class, HavanaFerriesService::class);
        $this->app->bind(FerryServiceInterface::class, BananaLinesService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

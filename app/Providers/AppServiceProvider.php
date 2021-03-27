<?php

namespace App\Providers;

use App\Services\Interfaces\CurlServiceInterface;
use App\Services\IxudraCurlService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CurlServiceInterface::class, IxudraCurlService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

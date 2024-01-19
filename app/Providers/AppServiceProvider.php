<?php

namespace App\Providers;

use App\Services\MessageAnalysisService;
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
        $this->app->bind(MessageAnalysisService::class, function ($app) {
            return new MessageAnalysisService();
        });
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

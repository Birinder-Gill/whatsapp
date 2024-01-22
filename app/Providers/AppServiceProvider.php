<?php

namespace App\Providers;

use App\Services\MessageAnalysisService;
use App\Services\MessageSendingService;
use App\Services\ReplyCreationService;
use App\Services\WhatsAppApiService;
use Illuminate\Http\Request;
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
        $this->app->bind(ReplyCreationService::class, function ($app) {
            return new ReplyCreationService($this->app->make(Request::class));
        });
        $this->app->bind(WhatsAppApiService::class, function ($app) {
            return new WhatsAppApiService();
        });
        $this->app->bind(MessageSendingService::class, function ($app) {
            return new MessageSendingService($this->app->make(ReplyCreationService::class), $this->app->make(WhatsAppApiService::class));
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

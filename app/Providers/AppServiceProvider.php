<?php

namespace App\Providers;

use App\Services\MessageSendingService;
use App\Services\ReplyCreationService;
use App\Services\WhatsAppApiService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use App\Services\OpenAiAnalysisService;
use App\Services\Products\JewellerTags;
use App\Services\Products\MagnifierLens;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(OpenAiAnalysisService::class, function ($app) {
            return new OpenAiAnalysisService();
        });
        $this->app->bind(ReplyCreationService::class, function ($app) {
            $productType = config('app.product');

            switch ($productType) {
                case 'Lens': return new MagnifierLens($this->app->make(Request::class));
                case 'Tags': return new JewellerTags($this->app->make(Request::class));
                default: throw new \Exception("Invalid product type");
            }

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

<?php

namespace App\Providers;

use App\Utils\CommunicationLogService;
use App\Utils\FriendlyExceptionErrorBag;
use Illuminate\Container\Container;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(CommunicationLogService::class, function() {
            return new CommunicationLogService( fn() => Container::getInstance() );
        });
        $this->app->singleton(FriendlyExceptionErrorBag::class, function() {
            return new FriendlyExceptionErrorBag( fn() => Container::getInstance() );
        });
    }
}

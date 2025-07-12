<?php

namespace App\Providers;

use App\Services\FonnteService;
use App\Channels\WhatsAppChannel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

class FonnteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('fonnte', function($app) {
            return new FonnteService();
        });
    }

    public function boot()
    {
        Notification::extend('fonnte', function ($app) {
            return new WhatsAppChannel($app->make(FonnteService::class));
        });
    }
}

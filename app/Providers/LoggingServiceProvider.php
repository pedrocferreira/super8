<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Log\LogManager;
use Monolog\Handler\NullHandler;

class LoggingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('log', function ($app) {
            return new \Monolog\Logger('null', [new NullHandler()]);
        });
    }
} 
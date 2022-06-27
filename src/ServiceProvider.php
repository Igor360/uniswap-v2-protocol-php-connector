<?php

namespace Igor360\UniswapV2Connector;

use Igor360\UniswapV2Connector\Configs\Config;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => \config_path(Config::BASE_KEY . '.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->app->bind(UniswapV2Connector::class, fn() => new UniswapV2Connector());
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', Config::BASE_KEY);
    }
}

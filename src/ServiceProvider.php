<?php

namespace Igor360\UniswapV2Connector;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('uniswap-v2-connector.php'),
            ], 'config');

            /*
            $this->loadViewsFrom(__DIR__.'/../resources/views', ':package_name');

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/:package_name'),
            ], 'views');
            */
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', '');
    }
}

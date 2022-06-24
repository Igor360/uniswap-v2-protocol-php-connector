<?php

namespace Igor360\UniswapV2Connector;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
    protected static function getFacadeAccessor()
    {
        return ':package_name';
    }
}

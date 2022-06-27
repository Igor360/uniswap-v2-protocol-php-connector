<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector;

use Illuminate\Support\Facades\Facade as LaravelFacade;

/**
 * @class Facade
 */
class Facade extends LaravelFacade
{
    protected static function getFacadeAccessor()
    {
        return UniswapV2Connector::class;
    }
}

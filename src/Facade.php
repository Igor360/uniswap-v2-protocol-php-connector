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
        return "igor360/uniswap-v2-protocol-php-connector";
    }
}

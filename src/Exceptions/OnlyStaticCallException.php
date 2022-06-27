<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Exceptions;

class OnlyStaticCallException extends \Exception
{
    protected $message = ERROR_MESSAGES::STATIC_CALL;
}

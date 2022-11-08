<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Exceptions;

/**
 * @class InvalidMethodCallException
 * @description Error for invalid call method
 */
class InvalidMethodCallException extends \Exception
{
    protected $message = ERROR_MESSAGES::INVALID_CALL;
}

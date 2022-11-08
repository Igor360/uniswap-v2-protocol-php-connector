<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Exceptions;

/**
 * @class InvalidAddressException
 * @description Exception for invalid address format
 */
class InvalidAddressException extends \Exception
{
    protected $message = ERROR_MESSAGES::INVALID_ADDRESS;
}

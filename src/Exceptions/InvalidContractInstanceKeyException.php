<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Exceptions;

/**
 * Invalid key call in contract factory
 */
class InvalidContractInstanceKeyException extends \Exception
{
    protected $message = ERROR_MESSAGES::INVALID_CONTRACT_KEY;
}

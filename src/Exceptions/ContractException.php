<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Exceptions;

class ContractException extends \Exception
{
    protected $message = ERROR_MESSAGES::CONTRACT;
}

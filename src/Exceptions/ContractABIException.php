<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Exceptions;

/**
 * @class ContractABIException
 * @description Exception in processing contract data using abi file
 */
class ContractABIException extends \Exception
{
    protected $message = ERROR_MESSAGES::ABI;
}

<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Exceptions;

/**
 * Error messages constants
 */
interface ERROR_MESSAGES
{
    public const BAD_REQUEST = "Invalid request was send";

    public const GETH = "Geth client error response";

    public const INVALID_CALL = "Called field not found, please realize getter in format 'getFieldName()'";

    public const INVALID_ADDRESS = "Invalid address format";

    public const ABI = "Invalid function decode";
}

<?php

namespace Igor360\UniswapV2Connector\Exceptions;

class BadRequestException extends \Exception
{
    protected $message = ERROR_MESSAGES::BAD_REQUEST;
}

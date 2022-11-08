<?php

namespace Igor360\UniswapV2Connector\Exceptions;

class InvalidImplementationClassException extends \Exception
{
    protected $message = ERROR_MESSAGES::INVALID_IMPLEMENTATION;
}

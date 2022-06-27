<?php

namespace Igor360\UniswapV2Connector;

use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Utils\ConnectionsUtils;

class UniswapV2Connector
{
    use ConnectionsUtils;

    public function __construct(?ConnectionInterface $credentials = null)
    {
        $this->handleEmptyConnection($credentials);
    }

    public function loadPair() {

    }
}
